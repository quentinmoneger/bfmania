
window.onload = () => {
    // On se connecte au serveur socket
    const socket = io("//localhost:3000");
    // On gère l'arrivée d'un nouvel utilisateur
    socket.on("connect", () => {

        // A l'arrivé dans le salon general
        // On charge tous les messages avec la fonction init_messages
        socket.emit("enter_room", "general");
    });

    // On écoute l'évènement submit
    document.querySelector("#send-chat").addEventListener("submit", (e) => {
        // On empêche l'envoi du formulaire
        e.preventDefault();
        const uuid = document.querySelector("#uuid")
        const message = document.querySelector("#message-chat");
        // On récupère le nom de la salle
        const room = document.querySelector("#tabs li.active").dataset.room;
        const createdAt = new Date();

        // On envoie le message
        socket.emit("chat_message", {
            uuid: uuid.value,
            message: message.value,
            room: room,
            createdAt: createdAt
        });

        // On efface le message
        document.querySelector("#message-chat").value = "";
    });

    // On écoute l'évènement "received_message"
    socket.on("received_message", (msg) => {
        publishMessages(msg);
    })

    
    // On écoute le clic sur les onglets
    document.querySelectorAll("#tabs li").forEach((tab) => {
        tab.addEventListener("click", function(){
            // On vérifie si l'onglet n'est pas actif
            if(!this.classList.contains("active")){
                // On récupère l'élément actuellement actif
                const actif = document.querySelector("#tabs li.active");
                actif.classList.remove("active");
                this.classList.add("active");
                document.querySelector("#messages").innerHTML = "";
                // On quitte l'ancienne salle
                socket.emit("leave_room", actif.dataset.room);
                // On entre dans la nouvelle salle
                socket.emit("enter_room", this.dataset.room);
            }
        })
    });

    // On écoute l'évènement "init_messages"
    socket.on("init_messages", msg => {
        let data = JSON.parse(msg.messages);

        if(data != []){
            data.forEach(donnees => {
                publishMessages(donnees);
            })
        }

    });

    // On écoute la frappe au clavier
    document.querySelector("#message-chat").addEventListener("input", () => {
        // On récupère l'uuid
        const uuid = document.querySelector("#uuid").value;
        // On récupère le salon
        const room = document.querySelector("#tabs li.active").dataset.room;

        socket.emit("typing", {
            uuid: uuid,
            room: room
        });
    });

    // On écoute les messages indiquant que quelqu'un tape au clavier
    // socket.on("usertyping", msg => {
    //     const writing = document.querySelector("#writing");

    //     writing.innerHTML = `${msg.name} tape un message...`;

    //     setTimeout(function(){
    //         writing.innerHTML = "";
    //     }, 5000);
    // });
}

function publishMessages(msg){
    moment.locale('fr');
    let date = moment(msg.createdAt).fromNow();  
    
    switch (msg.users[0].role) {
        case 99 :
            css = 'black-text';
            break;
        case 70 :
            css = 'red-text';
            break;
        case 50 :
            css = 'indigo-text text-darken-2 ';
            break;
        case 30 : 
            css = 'teal-text text-darken-2 ';
            break;
        default:
            css = '';
            break;
    }

    let texte = `
    <div class=" py-1 px-0 m-0" ><p class="d-flex justify-content-between p-0 m-0 font14"><span class="${css}">${msg.users[0].username}</span><small class="py-0 pr-4 ">${date}
    <i type="button" class="fa fa-comment-alt-exclamation px-1" data-toggle="modal" data-report-type="chat" data-username-from="${msg.users[0].username}" data-id="${msg.id}"  data-target="#report"></i></small></p>
    <p class=" pl-1 py-0  m-0 font12">${msg.message}</p>
    `
   
    document.querySelector("#messages").innerHTML += texte;

    var elem = document.getElementById('messages');
    elem.scrollTop = elem.scrollHeight;



}

