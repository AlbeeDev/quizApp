create database quizapp;

use quizapp;

create table user(
    id int primary key auto_increment,
    username varchar(255),
    email varchar(255),
    password varchar(255)
);

create table quiz(
    id int primary key auto_increment,
    name varchar(255),
    language varchar(255),
    fk_user int,
    foreign key (fk_user) references user(id)
);

create table question(
    id int primary key auto_increment,
    text text,
    image longblob,
    fk_quiz int,
    foreign key (fk_quiz) references quiz(id)
);

create table answer(
    id int primary key auto_increment,
    text text,
    is_correct int,
    fk_question int,
    foreign key (fk_question) references question(id)
);