<?php

class UsuarioChart extends TPage
{
    private $form; // form
    private $loaded;
    private static $database = 'ponto';
    private static $activeRecord = 'Usuario';
    private static $primaryKey = 'id';
    private static $formName = 'formChart_Usuario';

    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);

        // define the form title
        $this->form->setFormTitle("Usuários");

        $data_nascimento = new TDate('data_nascimento');

        $data_nascimento->setSize(110);
        $data_nascimento->setMask('dd/mm/yyyy');
        $data_nascimento->setDatabaseMask('yyyy-mm-dd');

        $row1 = $this->form->addFields([new TLabel("Nascidos até:", null, '14px', null)],[$data_nascimento]);

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        $btn_ongenerate = $this->form->addAction("Gerar", new TAction([$this, 'onGenerate']), 'fa:search #ffffff');
        $btn_ongenerate->addStyleClass('btn-primary'); 

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(TBreadCrumb::create(["Gráficos","Usuários"]));
        $container->add($this->form);

        parent::add($container);

    }

    /**
     * Register the filter in the session
     */
    public function onSearch()
    {
        // get the search form data
        $data = $this->form->getData();
        $filters = [];

        TSession::setValue(__CLASS__.'_filter_data', NULL);
        TSession::setValue(__CLASS__.'_filters', NULL);

        if (isset($data->data_nascimento) AND ( (is_scalar($data->data_nascimento) AND $data->data_nascimento !== '') OR (is_array($data->data_nascimento) AND (!empty($data->data_nascimento)) )) )
        {

            $filters[] = new TFilter('data_nascimento', '<=', $data->data_nascimento);// create the filter 
        }

        // fill the form with data again
        $this->form->setData($data);

        // keep the search data in the session
        TSession::setValue(__CLASS__.'_filter_data', $data);
        TSession::setValue(__CLASS__.'_filters', $filters);
    }

    /**
     * Load the datagrid with data
     */
    public function onGenerate()
    {
        try
        {
            $this->onSearch();
            // open a transaction with database 'ponto'
            TTransaction::open(self::$database);
            $param = [];
            // creates a repository for Usuario
            $repository = new TRepository(self::$activeRecord);
            // creates a criteria
            $criteria = new TCriteria;

            if ($filters = TSession::getValue(__CLASS__.'_filters'))
            {
                foreach ($filters as $filter) 
                {
                    $criteria->add($filter);       
                }
            }

            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);

            if ($objects)
            {

                $dataTotals = [];
                $groups = [];
                $data = [];
                foreach ($objects as $obj)
                {
                    $group1 = $obj->idade;
                    $group1 = TDateTime::convertToMask($group1, 'yyyy-mm-dd', 'Y/m');

                    $groups[$group1] = true;
                    $numericField = $obj->idade;

                    $dataTotals[$group1]['count'] = isset($dataTotals[$group1]['count']) ? $dataTotals[$group1]['count'] + 1 : 1;
                    $dataTotals[$group1]['sum'] = isset($dataTotals[$group1]['sum']) ? $dataTotals[$group1]['sum'] + $numericField  : $numericField;

                }

                $groups = ['x'=>true]+$groups;

                foreach ($dataTotals as $group1 => $totals) 
                {    

                    array_push($data, [$group1, $totals['sum']]);

                }

                $chart = new THtmlRenderer('app/resources/c3_pizza_chart.html');
                $chart->enableSection('main', [
                    'data'=> json_encode($data),
                    'height' => 300,
                    'precision' => 0,
                    'decimalSeparator' => '',
                    'thousandSeparator' => '',
                    'prefix' => '',
                    'sufix' => '',
                    'width' => 100,
                    'widthType' => '%',
                    'title' => 'Faixa etária',
                    'showLegend' => 'false',
                    'showPercentage' => 'true',
                    'barDirection' => 'false'
                ]);

                parent::add($chart);
            }
            else
            {
                new TMessage('error', _t('No records found'));
            }

            // close the transaction
            TTransaction::close();
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            // undo all pending operations
            TTransaction::rollback();
        }
    }

    public function onShow($param = null)
    {

    }

}

