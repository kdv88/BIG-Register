<?php

namespace Kdv\BigRegister;


class BigRecord
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