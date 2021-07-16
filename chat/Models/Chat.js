const Sequelize = require("sequelize");

module.exports = (sequelize, DataTypes) => {
    const Chat = sequelize.define("chat", {
        message: Sequelize.STRING,
        room: Sequelize.STRING,
        uuid_from: Sequelize.STRING
    }, {});
    return Chat;
};


 