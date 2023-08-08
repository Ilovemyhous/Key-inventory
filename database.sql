drop database if exists key_inventory;
create database key_inventory;
use key_inventory;

-- Users table
create table users (
    user_id int auto_increment not null,
    full_name varchar(255) not null,
    primary key (user_id)
);

-- Key table
create table `keys` (
    key_id int not null,
    amount int not null,
    inUse int default 0 not null,
    user_id int,
    primary key (key_id),
    foreign key (user_id) references users(user_id)
);

-- Lockers table
create table lockers (
    locker_id varchar(10) not null,
    location varchar(50) not null,
    notes varchar(255),
    user_id int,
    primary key (locker_id),
    foreign key (user_id) references users(user_id)
);


-- Insert keys
insert into `keys` (key_id, amount) values (0, 2147483647);
insert into `keys` (key_id, amount) values (4002, 8);
insert into `keys` (key_id, amount) values (4003, 45);
insert into `keys` (key_id, amount) values (4004, 11);
insert into `keys` (key_id, amount) values (4006, 13);
insert into `keys` (key_id, amount) values (4007, 29);
insert into `keys` (key_id, amount) values (4011, 1);
insert into `keys` (key_id, amount) values (4013, 8);
insert into `keys` (key_id, amount) values (4015, 3);
insert into `keys` (key_id, amount) values (4017, 6);
insert into `keys` (key_id, amount) values (4020, 10);
insert into `keys` (key_id, amount) values (4060, 7);

-- Insert users
insert into users (full_name) values ('John Doe');
insert into users (full_name) values ('Max Mustermann');

-- Insert lockers
insert into lockers (locker_id, location, notes) values ('1.C4.A1', 'Technic (Open space)', '');
insert into lockers (locker_id, location, notes) values ('1.C4.A2', 'Technic (Open space)', '');
insert into lockers (locker_id, location, notes) values ('1.C4.A3', 'Technic (Open space)', '');
insert into lockers (locker_id, location, notes) values ('1.C4.A4', 'Technic (Open space)', '');
insert into lockers (locker_id, location, notes) values ('2.CF.A3', 'Technic (Open space)', 'Garderobe');

-- The following table is a "selection" (from the user) of keys, users and lockers. The user can select a key, and assign it to a locker.

create table selection (
    selection_id int auto_increment not null,
    user_id int not null,
    full_name varchar(255) not null,
    locker_id varchar(10) not null,
    location varchar(50) not null,
    notes varchar(255),
    key_id int not null,
    primary key (selection_id)
);
ALTER TABLE selection CHANGE user_id user_id INT(11) NOT NULL DEFAULT '0';
SELECT * FROM selection;