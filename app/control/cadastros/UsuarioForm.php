<?php

class UsuarioForm extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = 'ponto';
    private static $activeRecord = 'Usuario';
    private static $primaryKey = 'id';
    private static $formName = 'form_Usuario';

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        // define the form title
        $this->form->setFormTitle("Cadastro de usuário");


        $id = new TEntry('id');
        $nome = new TEntry('nome');
        $data_nascimento = new TDate('data_nascimento');
        $email = new TEntry('email');

        $data_nascimento->setExitAction(new TAction([$this,'OnIdade']));

        $nome->addValidation("Nome", new TRequiredValidator()); 
        $data_nascimento->addValidation("Data nascimento", new TRequiredValidator()); 
        $email->addValidation("Email", new TRequiredValidator()); 

        $data_nascimento->setMask('dd/mm/yyyy');
        $id->setEditable(false);
        $data_nascimento->setDatabaseMask('yyyy-mm-dd');

        $id->setSize(100);
        $nome->setSize('70%');
        $email->setSize('70%');
        $data_nascimento->setSize(110);

        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', null)],[$id]);
        $row2 = $this->form->addFields([new TLabel("Nome:", '#ff0000', '14px', null)],[$nome]);
        $row3 = $this->form->addFields([new TLabel("Data nascimento:", '#ff0000', '14px', null)],[$data_nascimento]);
        $row4 = $this->form->addFields([new TLabel("Email:", '#ff0000', '14px', null)],[$email]);

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'fa:floppy-o #ffffff');
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fa:eraser #dd5a43');

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);

        parent::add($container);

    }

    public static function OnIdade($param = null) 
    {
        try 
        {
            //code here

        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    public function onSave($param = null) 
    {
        try
        {
            TTransaction::open(self::$database); // open a transaction

            /**
            // Enable Debug logger for SQL operations inside the transaction
            TTransaction::setLogger(new TLoggerSTD); // standard output
            TTransaction::setLogger(new TLoggerTXT('log.txt')); // file
            **/

            $messageAction = null;

            $this->form->validate(); // validate form data

            $object = new Usuario(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            // Save User and DateTime
            if (empty($object->id))
            {
                $object->criado_por_id = TSession::getValue('userid');
                $object->criado_em = date('Y-m-d H:i:s');
                $object->alterado_por_id = TSession::getValue('userid');
                $object->alterado_em = date('Y-m-d H:i:s');
            } else {
                $object->alterado_por_id = TSession::getValue('userid');
                $object->alterado_em = date('Y-m-d H:i:s');
            }

            // Save Age
            $nascimento    = new DateTime($object->data_nascimento);
            $dtregistro    = new DateTime($object->alterado_em);
            $idade         = $nascimento->diff($dtregistro);
            $object->idade = $idade->format("%Y");

            $object->store(); // save the object 

            $messageAction = new TAction(['UsuarioList', 'onShow']);   

            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            /**
            // To define an action to be executed on the message close event:
            $messageAction = new TAction(['className', 'methodName']);
            **/

            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'), $messageAction);

        }
        catch (Exception $e) // in case of exception
        {
            //</catchAutoCode> 

            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }

    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open(self::$database); // open a transaction

                $object = new Usuario($key); // instantiates the Active Record 

                $this->form->setData($object); // fill the form 

                TTransaction::close(); // close the transaction 
            }
            else
            {
                $this->form->clear();
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }

    /**
     * Clear form data
     * @param $param Request
     */
    public function onClear( $param )
    {
        $this->form->clear(true);

    }

    public function onShow($param = null)
    {

    } 

}

