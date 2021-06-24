<?php
class BuildEntity
{
    public $attrs = [];
    public $content = '';
    public $table = '';


    public function __construct($table)

    {
        $this->table = $table;
        $this->content = '<?php
        namespace Entity;

        use \Lib\Entity;
        
     class ' . $table . ' extends Entity
     {
        ';

        $this->getAttributes();
        $this->createAttributes();
        $this->createConstants();
        $this->createSetters();
        $this->createGetters();
        $this->content .= '}';

        $file = fopen('entity.temp/' . $this->table . '.php', 'w');
        fwrite($file, $this->content);
    }


    public function getAttributes()
    {
        $file = fopen(__DIR__ . "/" . $this->table . '.attributes.txt', 'r');
        while (!feof($file)) {
            $line = fgets($file);
            $this->attrs[] = trim(preg_replace('/\s+/', ' ', $line));
        }
    }

    public function createAttributes()
    {
        $this->content .= 'protected 
        ';

        $numItems = count($this->attrs);
        $i = 0;
        foreach ($this->attrs as $attr) {
            $this->content .= '$' . $attr;

            if (++$i === $numItems) {
                $this->content .=  ";
                ";
                return;
            }

            $this->content .= ', 
            ';
        }
    }

    public function createConstants()
    {

        $i = 1;
        foreach ($this->attrs as $attr) {
            $this->content .=
                '
                const ' . strtoupper($attr) . '_INVALIDE = ' . $i . ';';

            $i++;
        }

        $this->content .= '
        ';
    }

    public function createSetters()
    {

        foreach ($this->attrs as $attr) {

            if (strpos($attr, "date") === false) {
                $this->content .=
                    '
                    public function set' . ucfirst($attr) . '($' . $attr . ')
                    {
                        if (is_string($' . $attr . ') && !empty($' . $attr . ')) {
                            $this->' . $attr . ' = $' . $attr . ';
                        }else{
                            $this->erreurs[] = self::' . strtoupper($attr) . '_INVALIDE;
                        }
                    }
                    ';
            } else {
                $this->content .=
                    '
                    public function set' . ucfirst($attr) . '($' . $attr . ')
                    {
                        if ($this->validDate($' . $attr . ')) {
                            $this->' . $attr . ' = $' . $attr . ';
                        }else{
                            $this->erreurs[] = self::' . strtoupper($attr) . '_INVALIDE;
                        }
                    }
                    ';
            }
        }
    }

    public function createGetters()
    {

        foreach ($this->attrs as $attr) {
            $this->content .=
                '
                public function ' . $attr . '()
            {
                return $this->' . $attr . ';
                }
                ';
        }
    }
}