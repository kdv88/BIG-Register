<?php
require 'nusoap.php';

class BIGRepository
{
    protected $client;
    protected $namespace = 'http://services.cibg.nl/ExternalUser';
    private $professionalGroups = [];
    
    protected function getClient()
    {
        if ($this->client) {
            return $this->client;
        }
        
        return $this->client = new nusoap_client(
            'https://webservices.cibg.nl/Ribiz/OpenbaarV4.asmx'
        );
    }
    
    protected function fetchByParameters($params)
    {
        $params['WebSite'] = 'Ribiz';
        $client = $this->getClient();

        $soapVals = array();
        foreach ($params as $key => $value) {
            $soapVals[] = new soapval($key, null, $value, $this->namespace);
        }
        
        $result = $client->call(
            'listHcpApproxRequest', 
            $soapVals,
            $this->namespace,
            'http://services.cibg.nl/ExternalUser/ListHcpApprox4'
        );

        if (empty($result) || !isset($result['ListHcpApprox4'])) {
            return false;
        }
        
        return new BIGRecord($result['ListHcpApprox4']);
    }

    public function fetchByRegistrationNumber($id)
    {
        return $this->fetchByParameters(array(
            'RegistrationNumber' => $id
        ));
    }
    
    public function fetchByNameAndBirthdate($name, Datetime $birthdate)
    {
        return $this->fetchByParameters(array(
            'Name'          => $name,
            'DateOfBirth'   => $birthdate->format('Y-m-d')
        ));
    }
    
    public function fetchByNameAndInitials( $name, $initials, $params = [] ) {
        $params['Name'] = $name;
        $params['Initials'] = $initials;
        
        return $this->fetchByParameters($params);
    }
    
    public function fetchRaw( $params ) {
        return $this->fetchByParameters( $params );
    }
    
    public function fetchProfessionalGroups( $force = false ) {
        if( !empty( $this->professionalGroups ) && !$force ) return $this->professionalGroups;
        
        $params['WebSite'] = 'Ribiz';
        $client = $this->getClient();
        
        $soapVals = array();
        foreach ($params as $key => $value) {
            $soapVals[] = new soapval($key, null, $value, $this->namespace);
        }
        
        $result = $client->call(
            'GetRibizReferenceDataRequest', 
            $soapVals,
            $this->namespace,
            'http://services.cibg.nl/ExternalUser/GetRibizReferenceData'
        );
        
        return $result;
    }
    
    public function getProfessionalGroup( $code ) {
        $result = $this->fetchProfessionalGroups();
        $professionalGroup = $result['ProfessionalGroups']['ProfessionalGroup'];

        foreach( $professionalGroup as $group ) {
            if( $group['Code'] == $code  ) {
                return $group;
            }
        }
    }
}

class BIGRecord
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
    
    public function getData()
    {
        return $this->data;
    }
    
    public function getFullName()
    {
        if (empty($this->data['Initial']) || empty($this->data['BirthSurname'])) {
            return;
        }
    
        return $this->data['Initial'].' '.$this->data['BirthSurname'];
    }
    
    public function getRegistrationNumber()
    {
        $data = $this->data['ArticleRegistration']['ArticleRegistrationExtApp'];
        
        if( is_array( $data ) && count( $data ) > 0  ) { 
            $return = [];
            foreach( $data as $d ) {
                $return[] = $d['ArticleRegistrationNumber'];
            }
            
            return $return;
        }
        
        
        if (empty($this->data['ArticleRegistration']['ArticleRegistrationExtApp']['ArticleRegistrationNumber'])) {
            return;
        }
        
        return $this->data['ArticleRegistration']['ArticleRegistrationExtApp']['ArticleRegistrationNumber'];
    }
    
    public function getArticleRegistration() {
        if (empty($this->data['ArticleRegistration'])) {
            return [];
        }
        
        return $this->data['ArticleRegistration'];
    }
}
