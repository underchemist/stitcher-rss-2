<?php declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Feed;
use App\Action\RefreshShow;
use App\User;

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
    protected $description = 'Refresh cache for feeds';

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

        // Need to get a valid User's ID

        $user = User::whereRaw('expiration > NOW()')->first();

        if ($user === null) {
            $this->error("Could not find a user with an active premium subscription.");
            exit(1);
        }

        if ($id) {
            $feed = Feed::firstOrNew(['id' => $id]);

            if (!$force && !$feed->dueForRefresh()) {
                $this->error("Feed is not due for update.");
                exit(1);
            }

            return $this->action->refresh($feed, $user->stitcher_id);
        }

        Feed::chunk(100, function ($feeds) use ($force, $quick, $user) {
            foreach ($feeds as $feed) {
                if (!$force && !$feed->dueForRefresh()) {
                    continue;
                }

                $this->action->refresh($feed, $user->stitcher_id);

                if (!$quick) {
                    sleep(2);
                }
            }
        });
    }
}
