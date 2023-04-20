<?php
    namespace BlogEngine {
        class Configuration {
            public function SendData($data) {
                $template = file_get_contents(__DIR__ . "/template.txt");
    
                for($i = 0; $i < sizeof(array_keys($data)); $i++){
                    $key = array_keys($data)[$i];
                    $value = $data[$key];
                    $template = str_replace("{{$key}}", $value, $template);
                }
    
                $this->CreateConfigFile($template);
            }
    
            public function CreateConfigFile($data){
                $config = fopen("_config.php", "w");
                fwrite($config, $data);
                fclose($config);
            }
        }
    }
