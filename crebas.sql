/*==============================================================*/
/* Nom de SGBD :  MySQL 5.0                                     */
/* Date de création :  25/03/2014 13:50:13                      */
/*==============================================================*/


drop table if exists COMPOSANTE;

drop table if exists CURSUS;

drop table if exists ENSEIGNANT;

drop table if exists EPREUVE;

drop table if exists ETAPE;

drop table if exists ETUDIANT;

drop table if exists REGIME;

drop table if exists VALIDATION;

/*==============================================================*/
/* Table : COMPOSANTE                                           */
/*==============================================================*/
create table COMPOSANTE
(
   NUMCOMPOSANTE        int not null,
   LIBCOMPOSANTE        varchar(1024) not null,
   primary key (NUMCOMPOSANTE)
);

/*==============================================================*/
/* Table : CURSUS                                               */
/*==============================================================*/
create table CURSUS
(
   CODECURSUS           char(1) not null,
   LIBCURSUS            varchar(300) not null,
   NIVEAU               int not null,
   primary key (CODECURSUS)
);

/*==============================================================*/
/* Table : ENSEIGNANT                                           */
/*==============================================================*/
create table ENSEIGNANT
(
   NUMENSEIGNANT        int not null,
   NOMENSEIGNANT        varchar(100) not null,
   PERNOMENSEIGNANT     varchar(100) not null,
   LOGINENSEIGNANT      varchar(100) not null,
   MDPENSEIGNANT        varchar(100) not null,
   primary key (NUMENSEIGNANT)
);

/*==============================================================*/
/* Table : EPREUVE                                              */
/*==============================================================*/
create table EPREUVE
(
   IDEPREUVE            int not null,
   NUMCOMPOSANTE        int not null,
   LIBEPREUVE           varchar(3) not null,
   primary key (IDEPREUVE)
);

/*==============================================================*/
/* Table : ETAPE                                                */
/*==============================================================*/
create table ETAPE
(
   CODEETAPE            varchar(10) not null,
   CODECURSUS           char(1) not null,
   NUMCOMPOSANTE        int not null,
   LIBCOURTETAPE        varchar(100) not null,
   VERSIONETAPE         int not null,
   LIBLONGETAPE         varchar(300) not null,
   primary key (CODEETAPE)
);

/*==============================================================*/
/* Table : ETUDIANT                                             */
/*==============================================================*/
create table ETUDIANT
(
   NUMETUDIANT          varchar(8) not null,
   NUMREGIME            int not null,
   CODEETAPE            varchar(10) not null,
   NOMETUDIANT          varchar(100) not null,
   PRENOMETUDIANT       varchar(100) not null,
   MAILETUDIANT         varchar(200) not null,
   DATENAISETUDIANT     date not null,
   LOGINETUDIANT        varchar(100) not null,
   MDPETUDIANT          varchar(100) not null,
   DATEIAEETUDIANT      date not null,
   DATEIAC2IETUDIANT    date,
   primary key (NUMETUDIANT)
);

/*==============================================================*/
/* Table : REGIME                                               */
/*==============================================================*/
create table REGIME
(
   NUMREGIME            int not null,
   LIBREGIME            varchar(1024) not null,
   primary key (NUMREGIME)
);

/*==============================================================*/
/* Table : VALIDATION                                           */
/*==============================================================*/
create table VALIDATION
(
   IDVALIDATION         int not null,
   NUMENSEIGNANT        int not null,
   NUMETUDIANT          varchar(8) not null,
   IDEPREUVE            int not null,
   DATEVALIDATION       date not null,
   primary key (IDVALIDATION)
);

alter table EPREUVE add constraint FK_APPARTENIR_3 foreign key (NUMCOMPOSANTE)
      references COMPOSANTE (NUMCOMPOSANTE) on delete restrict on update restrict;

alter table ETAPE add constraint FK_COMPOSER foreign key (NUMCOMPOSANTE)
      references COMPOSANTE (NUMCOMPOSANTE) on delete restrict on update restrict;

alter table ETAPE add constraint FK_CORRESPONDRE foreign key (CODECURSUS)
      references CURSUS (CODECURSUS) on delete restrict on update restrict;

alter table ETUDIANT add constraint FK_APPARTENIR foreign key (NUMREGIME)
      references REGIME (NUMREGIME) on delete restrict on update restrict;

alter table ETUDIANT add constraint FK_APPARTENIR_2 foreign key (CODEETAPE)
      references ETAPE (CODEETAPE) on delete restrict on update restrict;

alter table VALIDATION add constraint FK_CORRESPONDRE_2 foreign key (IDEPREUVE)
      references EPREUVE (IDEPREUVE) on delete restrict on update restrict;

alter table VALIDATION add constraint FK_PASSER foreign key (NUMETUDIANT)
      references ETUDIANT (NUMETUDIANT) on delete restrict on update restrict;

alter table VALIDATION add constraint FK_VALIDER foreign key (NUMENSEIGNANT)
      references ENSEIGNANT (NUMENSEIGNANT) on delete restrict on update restrict;

