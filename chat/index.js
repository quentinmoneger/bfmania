// On instancie express
const { captureRejectionSymbol } = require("events");   
const express = require("express");
const app = express();

// On instancier striptags
const striptags = require("striptags");

// On charge "path"
const path = require("path");

// On autorise le dossier "public"
app.use(express.static(path.join(__dirname, "public")));

// On crée le serveur http
const http = require("http").createServer(app);

// On instancie socket.io
const io = require("socket.io")(http, {
    cors: {
        /* https://github.com/expressjs/cors#configuration-options */
        /*origin: config.host_cors,*/
        origin: '*',
        methods: ['GET', 'PUT', 'POST'],
        allowedHeaders: ['Content-Type', 'Accept', 'Origin', 'X-Requested-With']
    }
});

// On charge sequelize
const Sequelize = require("sequelize");

// On se connecte à la base
const sequelize = new Sequelize("bfmania", "root", "", {
    host: "localhost",
    dialect: "mysql",
    logging: false,

});

// On charge le modèle "Chat"
const Chat = require("./Models/Chat")(sequelize, Sequelize.DataTypes);

// On charge le modèle "User"
const User = require("./Models/User")(sequelize, Sequelize.DataTypes);

Chat.hasMany(User, {     
    foreignKey: 'uuid',
    sourceKey: 'uuid_from'
});

User.belongsTo(Chat, {
    foreignKey: 'uuid',
});


// On crée la route /
app.get("/", (req, res) => {
    res.sendFile("C:/xampp/htdocs/bfmania.com/app/Views/front/chat/index.html.php");
});

// On écoute l'évènement "connection" de socket.io
io.on("connection", (socket) => {
    console.log("Une connexion s'active");

    // On écoute les déconnexions
    socket.on("disconnect", () => {
        console.log("Un utilisateur s'est déconnecté");
    });

    // On écoute les entrées dans les salles
    socket.on("enter_room", (room) => {

        const allowedRooms = ['general', 'games'];

        if (allowedRooms.includes(room)) {
            // On entre dans la salle demandée
            socket.join(room);
            console.log(socket.rooms);


            // On envoie tous les messages du salon
            Chat.findAll({
                attributes: ["id", "message", "room", "createdAt", "uuid_from"],
                where: {
                    room: room
                },
                order: [['createdAt', 'ASC']],
                include: [{
                    model: User,
                    required: true
                }]
            }).then(list => {
                socket.emit("init_messages", { messages: JSON.stringify(list) });
            });
        }
    });

    // On écoute les sorties dans les salles
    socket.on("leave_room", (room) => {
        // On entre dans la salle demandée
        socket.leave(room);
        console.log(socket.rooms);
    });

    // On gère le chat
    socket.on("chat_message", (msg) => {
        
        // On stocke le message dans la base

        new_msg = {
            uuid_from: striptags(msg.uuid),
            message: striptags(msg.message),
            room: striptags(msg.room),
            createdAt: striptags(msg.createdAt)
        }
    
        User.findOne({
            where: {uuid: msg.uuid}
        }).then((user) => {

            var user = user

            Chat.create({

                uuid_from: new_msg.uuid_from,
                message: new_msg.message,
                room: new_msg.room,
                createdAt: new_msg.createdAt

            }).then(() => {

                msg.users = [user];

                // Le message est stocké, on le relaie à tous les utilisateurs dans le salon correspondant
                io.in(msg.room).emit("received_message", msg); 
            }).catch(e => {
                console.log(e);
            });

        })
        .catch((err) => {
            console.log("Error while find user : ", err)
        })

        

    });

    // On écoute les messages "typing"
    socket.on("typing", msg => {
        socket.to(msg.room).emit("usertyping", msg);
    })
    // }


});

// On va demander au serveur http de répondre sur le port 3000
http.listen(3000, () => {
    console.log("J'écoute le port 3000");
});
