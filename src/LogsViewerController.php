<?php
    /**
     * Copyright (C) 2016  Quatro Design by Jakub Socha
     *
     *
     *
     * @file       : TokenGenerator.php
     * @author     : Jakub Socha
     * @copyright  : (c) 2009-2016 Quatro Design
     * @link       : http://quatrodesign.pl
     * @date       : 08.12.16
     * @version    : 1.0.0
     */
    
    namespace Jsocha\LogsViewer;
    
    use App\Http\Controllers\Controller;
    use Carbon\Carbon;
    use DirectoryIterator;
    use Illuminate\Support\Facades\File;
    
    class LogsViewerController extends Controller
    {
        public $route = 'logs.viewer';
        
        final public function index($file = null, $action = null)
        {
            $detailedLog = [];
            $logName = '';
            
            $logs = $this->getLogFiles();
            
            
            /**
             * Wybrano jakiś plik
             */
            if (! is_null($file)) {
                
                $logFile = $this->parseLog(base64_decode($file));
                $logName = base64_decode($file);
                
                $pattern = '/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\].*/';
                preg_match_all($pattern, $logFile, $parts);
                
                $stackTrace = preg_split($pattern, $logFile);
                
                if ($stackTrace[0] < 1) {
                    array_shift($stackTrace);
                }
                
                
                foreach ($parts[0] as $key => $part) {
                    $pattern = [];
                    
                    $stack = explode(' ', $part);
                    
                    $dateInfo = implode(' ', [$stack[0], $stack[1]]);
                    $date = trim(str_replace(['[', '] ', ']'], ['', '', ''], $dateInfo));
                    
                    $pattern['date'] = Carbon::createFromTimestamp(strtotime($date));
                    unset($stack[0]);
                    unset($stack[1]);
                    $pattern['error'] = implode(' ', $stack);
                    
                    /**
                     * Ikonki
                     */
                    if (preg_match('/local.ERROR/', $pattern['error'])) {
                        $pattern['icon'] = '<span class="btn btn-xs btn-danger"><i class="fa fa-exclamation-triangle"></i></span>';
                        
                        $pattern['error'] = str_replace('local.ERROR:', '', $pattern['error']);
                    }
                    elseif (preg_match('/local.INFO/', $pattern['error'])) {
                        $pattern['icon'] = '<span class="btn btn-xs btn-info"><i class="fa fa-info-circle"></i></span>';
                        
                        $pattern['error'] = str_replace('local.INFO:', '', $pattern['error']);
                    }
                    else {
                        $pattern['icon'] = '';
                    }
                    
                    $pattern['stack'] = $stackTrace[$key];
                    
                    $pattern['hash'] = md5($pattern['error']);
                    
                    
                    $detailedLog[] = $pattern;
                    
                }
                
            }
            
            $hashes = [];
            
            foreach ($detailedLog as $log) {
                if (isset($hashes[$log['hash']])) {
                    $hashes[$log['hash']]++;
                }
                else {
                    $hashes[$log['hash']] = 1;
                    
                }
            }
            $detailedLog = collect($detailedLog)->keyBy('hash')->toArray();
            
            
            /**
             * Kasowanie pliku
             */
            if ($action == 'delete') {
                $path = storage_path() . '/logs/' . $logName;
                
                if (File::exists($path)) {
                    File::delete($path);
                    
                    return redirect()->route($this->route);
                }
                else {
                    pr('There is no file on ' . $path);
                }
            }
            
            return app('view')->make('logs-viewer::index', ['logs'        => $logs,
                                                            'detailedLog' => array_reverse($detailedLog),
                                                            'logName'     => $logName,
                                                            'hashes'      => $hashes,
                                                            'route'       => $this->route]);
        }
        
        /**
         * Fetch or lofs files stored in /storage/logs
         *
         * @return array
         */
        
        final public function getLogFiles()
        {
            $files = [];
            
            $path = storage_path() . '/logs';
            
            $dir = new DirectoryIterator($path);
            
            foreach ($dir as $file) {
                if (! $file->isDot() && $file->getExtension() == 'log') {
                    $files[] = $file->getFilename();
                }
            }
            
            return $files;
        }
        
        final public function parseLog($file)
        {
            return file_get_contents(storage_path() . '/logs/' . $file);
        }
    }