<?php

namespace View;

use Lib\View;


class TestView extends View
{
    public function viewGetListHistoriqueService()
    {


        foreach ($this->response["test"] as $test) {
            $test['historiqueService'] = [];

            foreach ($this->response["historiqueService"] as $historiqueService) {
                if ($test['id'] == $historiqueService['id']) {

                    $arrayWithoutId = [];
                    foreach ($historiqueService as $key => $value) {
                        if ($key != 'id') {

                            $arrayWithoutId[$key] = $value;
                        }
                    }
                    $test['historiqueService'][] = $arrayWithoutId;
                }
            }

            $this->view[] = $test;
        }

        return $this->view;
    }

    public function viewGetHistoriqueService()
    {

        $test = $this->response["test"];
        $test['historiqueService'] = $this->response["historiqueService"];
        $this->view = $test;

        return $this->view;
    }
}