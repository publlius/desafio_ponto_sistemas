begin; 

CREATE TABLE usuario( 
      id number(10)    NOT NULL , 
      nome varchar  (200)    NOT NULL , 
      data_nascimento date    NOT NULL , 
      idade number(10)   , 
      email varchar  (240)    NOT NULL , 
      criado_em timestamp(0)   , 
      criado_por_id number(10)   , 
      alterado_em timestamp(0)   , 
      alterado_por_id number(10)   , 
 PRIMARY KEY (id)); 

  
  CREATE SEQUENCE usuario_id_seq START WITH 1 INCREMENT BY 1; 

CREATE OR REPLACE TRIGGER usuario_id_seq_tr 

BEFORE INSERT ON usuario FOR EACH ROW 

WHEN 

(NEW.id IS NULL) 

BEGIN 

SELECT usuario_id_seq.NEXTVAL INTO :NEW.id FROM DUAL; 

END;
 
  
 
 commit;