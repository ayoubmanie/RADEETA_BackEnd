<?php
class managerMaker
{
    public $attrs = [];
    public $content = '';
    public $filename = '';

    public function __construct($filename)

    {
        $this->filename = $filename;
        $this->content = '<?php
        
     class ' . $this->filename . 'ManagerPDO 
     {
        ';

        $this->getAttributes();

        $this->createAdd();


        $this->content .= '}';


        $file = fopen($filename . 'ManagerPDO.php', 'w+');
        fwrite($file, $this->content);
    }


    public function getAttributes()
    {
        $file = fopen('test.txt', 'r');
        while (!feof($file)) {
            $line = fgets($file);
            $this->attrs[] = trim(preg_replace('/\s+/', ' ', $line));
        }
    }

    public function createAdd()
    {
        $this->content .= 'protected function add(' . $this->filename . ' $' . strtolower($this->filename) . '){
        $requete = $this->dao->prepare("INSERT INTO ' . strtolower($this->filename) . ' SET ';

        $numItems = count($this->attrs);
        $i = 0;
        foreach ($this->attrs as $attr) {
            $this->content .= $attr . ' = :' . $attr;

            if (++$i === $numItems) {
                $this->content .=  '");
                
                ';
            } else $this->content .= ', ';
        }

        foreach ($this->attrs as $attr) {
            $this->content .= '$requete->bindValue(":' . $attr . '", $' . strtolower($this->filename) . '->' . $attr . '());
            ';
        }

        $this->content .= '
        $requete->execute();
    }';
    }
}