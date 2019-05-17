<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Feed;
use App\Action\RefreshShow;

class FeedCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '
        feed:cache
        { id? : Stitcher\'s Feed ID }
        { --force : Ignore refresh restrictions }
        { --quick : Ignore throttling restrictions }
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * @var RefreshShow $action
     */
    protected $action;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(RefreshShow $action)
    {
        $this->action = $action;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $id = $this->argument('id');
        $force = $this->option('force');
        $quick = $this->option('quick');

        if ($id) {
            $feed = Feed::firstOrNew(['id' => $id]);

            if (!$force && !$feed->dueForRefresh()) {
                $this->error("Feed is not due for update.");
                exit(1);
            }

            return $this->action->refresh($feed, $force);
        }

        Feed::chunk(100, function ($feeds) use ($force, $quick) {
            foreach ($feeds as $feed) {
                if (!$force && !$feed->dueForRefresh()) {
                    continue;
                }

                if (!$quick) {
                    $time = microtime(true);
                }

                $this->action->refresh($feed);

                if (!$quick) {
                    // Wait five seconds between refreshes
                    $time = microtime(true) - $time;
                    usleep((5 - $time) * 1000000);
                }
            }
        });
    }
}
