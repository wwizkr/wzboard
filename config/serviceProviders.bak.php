<?php
// 파일 위치: /config/serviceProviders.php

use Web\PublicHtml\Core\DependencyContainer;

function registerServices(DependencyContainer $container)
{
    // 자동 등록을 위한 서비스 스캔 대상 디렉토리들
    $serviceDirectories = [
        __DIR__ . '/../src/Admin/Service',
        __DIR__ . '/../src/Admin/Model',
        __DIR__ . '/../src/Admin/Helper',
        __DIR__ . '/../src/Service',
        __DIR__ . '/../src/Helper',
        __DIR__ . '/../src/Model',
    ];

    foreach ($serviceDirectories as $directory) {
        if (!is_dir($directory)) {
            //echo "Directory not found: $directory<br>";
            continue;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isDir()) {
                continue;
            }

            if ($file->getExtension() !== 'php') {
                continue;
            }

            $className = extractClassNameFromFile($file->getRealPath());
            //echo "Extracted class name: $className from file: " . $file->getRealPath() . "<br>";
            if (empty($className)) {
                echo "No class found in file: " . $file->getRealPath() . "<br>";
                continue;
            }

            //echo "Processing class: $className<br>";

            try {
                if (!class_exists($className, true)) {
                    throw new Exception("Class $className does not exist");
                }

                $reflection = new ReflectionClass($className);
                
                if (!$reflection->isInstantiable()) {
                    //echo "Class not instantiable: $className<br>";
                    continue;
                }

                $constructor = $reflection->getConstructor();
                
                if ($constructor && $constructor->getNumberOfParameters() > 0) {
                    //echo '<pre>';
                    //var_dump($className);
                    //echo '</pre>';
                    $container->addFactory($className, function ($c) use ($className) {
                        return new $className($c);
                    });
                    //echo "Registered with factory: $className<br>";
                } else {
                    //echo '<pre>';
                    //var_dump($className);
                    //echo '</pre>';
                    $container->set($className, new $className());
                    //echo "Registered directly: $className<br>";
                }
            } catch (Exception $e) {
                echo "Error processing $className: " . $e->getMessage() . "<br>";
            }
        }
    }
}

function extractClassNameFromFile($filePath) {
    $content = file_get_contents($filePath);
    $tokens = token_get_all($content);
    $namespace = '';
    $className = '';

    for ($i = 0; $i < count($tokens); $i++) {
        if (is_array($tokens[$i])) {
            $tokenName = token_name($tokens[$i][0]);
            
            if ($tokenName === 'T_NAMESPACE') {
                // 네임스페이스 추출
                $i += 1; // 네임스페이스 키워드 건너뛰기
                while (isset($tokens[$i]) && is_array($tokens[$i])) {
                    $currentTokenName = token_name($tokens[$i][0]);
                    //echo "Current token: " . $currentTokenName . " - " . $tokens[$i][1] . "<br>";
                    if ($currentTokenName === 'T_NAME_QUALIFIED' || $currentTokenName === 'T_STRING' || $currentTokenName === 'T_NS_SEPARATOR') {
                        $namespace .= $tokens[$i][1];
                    } else if ($tokens[$i] === ';' || $tokens[$i] === '{') {
                        break;
                    }
                    $i++;
                }
            }

            if ($tokenName === 'T_CLASS') {
                // 클래스 이름 추출
                $i += 2; // class 키워드와 공백 건너뛰기
                if (isset($tokens[$i]) && is_array($tokens[$i]) && token_name($tokens[$i][0]) === 'T_STRING') {
                    $className = $tokens[$i][1];
                    break;
                }
            }
        }
    }

    //echo "Extracted namespace: " . $namespace . "<br>";
    //echo "Extracted class name: " . $className . "<br>";

    if ($namespace && $className) {
        return $namespace . '\\' . $className;
    }
    return $className;
}