<?php
function menu(){

    $ManagerModel = model('ManagerModel');
    $Menu = $ManagerModel->Menu();
    $ItenMenu = $ManagerModel->ItenMenu();

    $resultMenu = "";

    if($Menu){
        foreach($Menu as $key => $DadosMenu){
            
            $manager = ($DadosMenu['id'] == 1) ? "text-danger" : "";

            if(MENU_VERTICAL){
                // menu vertical
                $resultMenu .= '<li '.checkMenuAtivo($DadosMenu['nome'], 1).'>';
                $resultMenu .= '<a href="javascript:void(0);">'.((strlen(trim($DadosMenu['icone'] ?? '')) > 0 ? $DadosMenu['icone'] : '<i class="mdi mdi-arrow-right '.$manager.'"></i>')).'<span class="'.$manager.'">'.$DadosMenu['menutit'].'</span></a>';

                    if($ItenMenu){
                        $resultMenu .= '<ul class="nav-second-level  '.checkMenuAtivo($DadosMenu['nome'], 2).'" aria-expanded="false">';
                        foreach($ItenMenu as $row => $DadosItenMenu){
                            if($DadosMenu['id'] == $DadosItenMenu['idpai']){
                                $resultMenu .= '<li><a href="'.base_url($DadosItenMenu['caminho']).'">'.$DadosItenMenu['menutit'].'</a></li>';
                            }
                        }
                        $resultMenu .= '</ul>';
                    }


                $resultMenu .= '</li>';

            }else{
                // menu horizontal
                $resultMenu .= '<li class="has-submenu">';
                $resultMenu .= '<a href="#">'.((strlen(trim($DadosMenu['icone'])) > 0 ? $DadosMenu['icone'] : '<i class="mdi mdi-arrow-right '.$manager.'"></i>')).'<span class="'.$manager.'">'.$DadosMenu['menutit'].'</span></a>';

                    if($ItenMenu){
                        $resultMenu .= '<ul class="submenu">';
                        foreach($ItenMenu as $row => $DadosItenMenu){
                            if($DadosMenu['id'] == $DadosItenMenu['idpai']){
                                $resultMenu .= '<li><a href="'.base_url($DadosItenMenu['caminho']).'">'.$DadosItenMenu['menutit'].'</a></li>';
                            }
                        }
                        $resultMenu .= '</ul>';
                    }


                $resultMenu .= '</li>';

            }

        }
    }

    return $resultMenu;

}