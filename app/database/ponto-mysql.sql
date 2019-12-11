begin; 

CREATE TABLE usuario( 
      id  INT  AUTO_INCREMENT    NOT NULL  , 
      nome varchar  (200)   NOT NULL  , 
      data_nascimento date   NOT NULL  , 
      idade int   , 
      email varchar  (240)   NOT NULL  , 
      criado_em datetime   , 
      criado_por_id int   , 
      alterado_em datetime   , 
      alterado_por_id int   , 
 PRIMARY KEY (id)); 

  
 
  
 
 commit;