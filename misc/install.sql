create table `extensiones`(
    `nombre` varchar(255) not null,
    `version` varchar(7) not null,
    `carpeta` varchar(100) not null,
    `loader` varchar(100) not null,
    `estado` varchar(15) not null default 'habilitada',
    `orden` int not null,
    `id` int auto_increment,
    primary key(id)
);

create table `funciones`(
    function varchar(120) not null,
    evento varchar(32) not null,
    estado varchar(15) not null default 'habilitada',
    orden int not null,
    extension_id int not null,
    id int auto_increment,
    primary key(id)
);

create table `mailing`(
    subject varchar(255) not null,
    body longtext not null,
    codename varchar(60) not null,
    id int auto_increment,
    primary key(id)
)

create table `errores`(
    archivo varchar(400) not null,
    linea int not null,
    codigo int not null,
    mensaje varchar(500) not null,
    fecha datetime not null,
    id int auto_increment,
    primary key(id)
)
