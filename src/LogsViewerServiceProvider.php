<?php
    
    namespace Jsocha\LogsViewer;
    
    
    use Illuminate\Support\ServiceProvider;
    
    class LogsViewerServiceProvider extends ServiceProvider
    {
        protected $defer = false;
        
        public function boot()
        {
            $this->loadViewsFrom(__DIR__ . '/views', 'logs-viewer');
            
            $this->loadRoutesFrom(__DIR__ . '/routes.php');
        }
        
    }