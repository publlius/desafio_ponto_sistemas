begin; 

CREATE TABLE usuario( 
      id  INT IDENTITY    NOT NULL  , 
      nome varchar  (200)   NOT NULL  , 
      data_nascimento date   NOT NULL  , 
      idade int   , 
      email varchar  (240)   NOT NULL  , 
      criado_em datetime2   , 
      criado_por_id int   , 
      alterado_em datetime2   , 
      alterado_por_id int   , 
 PRIMARY KEY (id)); 

  
 
  
 
 commit;