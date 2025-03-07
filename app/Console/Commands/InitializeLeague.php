<?php

namespace App\Console\Commands;

use App\Services\SimulationService;
use Illuminate\Console\Command;

class InitializeLeague extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'league:initialize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize the football league with teams and fixtures';

    /**
     * The simulation service instance.
     *
     * @var SimulationService
     */
    protected $simulationService;

    /**
     * Create a new command instance.
     *
     * @param SimulationService $simulationService
     * @return void
     */
    public function __construct(SimulationService $simulationService)
    {
        parent::__construct();
        $this->simulationService = $simulationService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Initializing teams...');
        $this->simulationService->initializeTeams();

        $this->info('Generating fixtures...');
        $this->simulationService->generateFixtures();

        $this->info('League initialized successfully!');

        return Command::SUCCESS;
    }
}
