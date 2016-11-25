/**
 * Author:  fcorrea
 * Created: 25/09/2016
 * Fernando H Corrêa 
 */
CREATE TABLE files(
    name_md5 char(100) primary key not null,
    name char(100) not null,
    chk_method char(50) not null,
    md5_file char(100) not null
);
