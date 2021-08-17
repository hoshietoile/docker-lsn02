-- docker cp create_words.sql vue-php-db:/app/create_words.sql
drop table if exists db.words;

create table db.words (
  id int AUTO_INCREMENT,
  sentence_id int,
  word_id int,
  surface_form varchar(50),
  basic_form varchar(50),
  conjugated_type varchar(50),
  pos varchar(50),
  pronunciation varchar(50),
  reading varchar(50),
  created_at timestamp not null default current_timestamp,
  PRIMARY KEY (id)
);