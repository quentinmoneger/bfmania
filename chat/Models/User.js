const Sequelize = require("sequelize");

module.exports = (sequelize, DataTypes) => {
    const User = sequelize.define("user", {
        username: Sequelize.STRING,
        uuid: Sequelize.STRING,
        role: Sequelize.INTEGER
    }, {
        tableName: 'users', 
    	timestamps: false // désactive les champs "createdAt", "updatedAt"
    } );  
    return User;
};
