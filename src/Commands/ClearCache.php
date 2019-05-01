<?php

namespace Poseso\Settings\Commands;

use Illuminate\Console\Command;
use Poseso\Settings\SettingsManager;

class ClearCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'settings:clear-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear cache settings';

    /**
     * The settings manager instance.
     *
     * @var \Poseso\Settings\SettingsManager
     */
    protected $manager;

    /**
     * Create a new command instance.
     *
     * @param \Poseso\Settings\SettingsManager $manager
     * @return void
     */
    public function __construct(SettingsManager $manager)
    {
        parent::__construct();

        $this->manager = $manager;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->manager->clearCache();
    }
}
