<?php
        namespace Entity;

        use \Lib\Entity;
        
     class HistoriqueService extends Entity
     {
        protected 
        $testId, 
            $serviceId, 
            $date;
                
                const TESTID_INVALIDE = 1;
                const SERVICEID_INVALIDE = 2;
                const DATE_INVALIDE = 3;
        
                    public function setTestId($testId)
                    {
                        if (is_string($testId) && !empty($testId)) {
                            $this->testId = $testId;
                        }else{
                            $this->erreurs[] = self::TESTID_INVALIDE;
                        }
                    }
                    
                    public function setServiceId($serviceId)
                    {
                        if (is_string($serviceId) && !empty($serviceId)) {
                            $this->serviceId = $serviceId;
                        }else{
                            $this->erreurs[] = self::SERVICEID_INVALIDE;
                        }
                    }
                    
                    public function setDate($date)
                    {
                        if ($this->validDate($date)) {
                            $this->date = $date;
                        }else{
                            $this->erreurs[] = self::DATE_INVALIDE;
                        }
                    }
                    
                public function testId()
            {
                return $this->testId;
                }
                
                public function serviceId()
            {
                return $this->serviceId;
                }
                
                public function date()
            {
                return $this->date;
                }
                }