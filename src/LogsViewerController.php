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
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\File;
    use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
    
    /**
     * Class LogsViewerController
     *
     * @package Jsocha\LogsViewer
     */
    class LogsViewerController extends Controller
    {
        /**
         * Name of route
         *
         * @var string
         */
        public $route = 'logs.viewer';
        
        
        /**
         * List of all logs
         *
         * @param Request $request
         *
         * @return \Illuminate\Http\RedirectResponse|mixed
         */
        final public function index(Request $request)
        {
            $file = $request->get('file', null);
            $action = $request->get('action', null);
            
            
            $getAllLogs = $this->getFiles();
            
            
            /**
             * Some file is selected
             */
            $log = ! is_null($file) ? $this->parseLog($file) : ['logs'   => [],
                                                                'name'   => null,
                                                                'unique' => []];
            
            
            /**
             * Some action is required
             */
            if (! is_null($action)) {
                
                switch ($action) {
                    case 'delete':
                        return $this->deleteFile($file);
                        break;
                    
                    case 'deleteMultiple':
                        return $this->deleteManyFiles($request->get('delete', []));
                        break;
                    
                    default:
                        throw  new NotFoundHttpException('Action ' . $action . ' not exists');
                }
            }
            
            
            return app('view')->make('logs-viewer::index', ['allLogs' => $getAllLogs,
                                                            'log'     => $log,
                                                            'message' => $request->get('message', null),
                                                            'route'   => $this->route]);
        }
        
        
        /**
         * Delete single file from server
         *
         * @param string $file
         *
         * @return \Illuminate\Http\RedirectResponse
         */
        final private function deleteFile(string $file)
        {
            $path = storage_path() . '/logs/' . base64_decode($file);
            
            if (File::exists($path)) {
                File::delete($path);
            }
            
            return redirect()->route($this->route);
            
        }
        
        /**
         * Delete many files at once
         *
         * @param array $files
         *
         * @return \Illuminate\Http\RedirectResponse
         */
        final private function deleteManyFiles(array $files)
        {
            foreach ($files as $file) {
                $path = storage_path() . '/logs/' . base64_decode($file);
                
                if (File::exists($path)) {
                    File::delete($path);
                }
            }
            
            return redirect()->route($this->route, ['message' => 'Files has been deleted']);
            
        }
        
        /**
         * Get all files stored in /storage/logs
         *
         * @return array
         */
        final private function getFiles(): array
        {
            $files = [];
            $path = storage_path() . '/logs';
            $dir = new DirectoryIterator($path);
            
            foreach ($dir as $file) {
                if (! $file->isDot() && $file->getExtension() == 'log') {
                    $files[$file->getFilename()] = $file->getFilename();
                }
            }
            rsort($files);
            
            return $files;
        }
        
        /**
         * Get log`s content
         *
         * @param string $file
         *
         * @return string
         */
        final private function getLog(string $file): string
        {
            return File::exists(storage_path() . '/logs/' . $file) ? file_get_contents(storage_path() . '/logs/' . $file) : '';
        }
        
        /**
         * Parse log`s content
         *
         * @param string $file
         *
         * @return array
         */
        final private function parseLog(string $file)
        {
            $logFile = $this->getLog(base64_decode($file));
            
            $detailedLog = [];
            
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
                
                $pattern['date'] = Carbon::parse($date);
                unset($stack[0]);
                unset($stack[1]);
                $pattern['error'] = implode(' ', $stack);
                
                $pattern['stack'] = $stackTrace[$key];
                $pattern['hash'] = md5($pattern['error']);
                
                $detailedLog[] = $pattern;
                
            }
            
            /**
             * Stack by md5() of error content to prevent duplictae on list
             */
            $unique = [];
            
            foreach ($detailedLog as $log) {
                if (isset($unique[$log['hash']])) {
                    $unique[$log['hash']]++;
                }
                else {
                    $unique[$log['hash']] = 1;
                }
            }
            $detailedLog = collect($detailedLog)->keyBy('hash')->toArray();
            
            return ['name'   => base64_decode($file),
                    'logs'   => $detailedLog,
                    'unique' => $unique];
            
        }
    }