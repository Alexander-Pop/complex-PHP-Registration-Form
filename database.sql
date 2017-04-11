create table accounts (
    uid int(10) auto_increment,
    username varchar(40) unique,
    passhash varchar(60) unique,
    email varchar(100) unique,
    creation timestamp default current_timestamp,
    `session` varchar(64) unique,
    primary key(uid));

