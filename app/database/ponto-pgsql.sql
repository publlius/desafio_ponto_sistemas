begin; 

CREATE TABLE usuario( 
      id  SERIAL    NOT NULL  , 
      nome varchar  (200)   NOT NULL  , 
      data_nascimento date   NOT NULL  , 
      idade integer   , 
      email varchar  (240)   NOT NULL  , 
      criado_em timestamp   , 
      criado_por_id integer   , 
      alterado_em timestamp   , 
      alterado_por_id integer   , 
 PRIMARY KEY (id)); 

  
 
  
 
 commit;