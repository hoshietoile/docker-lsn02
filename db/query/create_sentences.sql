-- docker cp create_sentences.sql vue-php-db:/app/create_sentences.sql
drop table if exists db.sentences;

create table db.sentences (
  id int AUTO_INCREMENT,
  sentence varchar(500),
  created_at timestamp not null default current_timestamp,
  PRIMARY KEY (id)
);