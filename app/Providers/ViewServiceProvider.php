<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Using class based composers...
        // View::composer(
        //     'profile', 'App\Http\View\Composers\ProfileComposer'
        // );

        // Using Closure based composers...
        View::composer(
            [
                'pages.dashboard.index',

                'pages.pipeline.index',

                'pages.deal.index',

                'pages.reminder.index',

                'pages.journal.index',

                'pages.panel.index',

                'pages.lead.index',

                'pages.whiteboard.index',
                'pages.whiteboard.combined',
                'pages.whiteboard.basic',
                'pages.whiteboard.business',
                'pages.whiteboard.marketing',

                'pages.team.index',
                'pages.team.broker',
                'pages.team.pipeline',
                'pages.team.combined',
                'pages.team.basic',

                'pages.calendar.index',

                'pages.search.index',

            ],
            'App\Http\View\Composers\CommonComposer'
        );

        View::composer(
            [
                'pages.deal.index',
            ],
            'App\Http\View\Composers\DealStatusComposer'
        );

        View::composer(
            [
                'pages.deal.contact.form',
            ],
            'App\Http\View\Composers\UserDataComposer'
        );

        View::composer(
            [
                'pages.configuration.index',
                'pages.configuration.profile',
                'pages.configuration.user',
                'pages.configuration.useredit',
                'pages.configuration.userform',
                'pages.configuration.changepassword',
                'pages.configuration.aggregator',
                'pages.configuration.formaggregator',
                'pages.configuration.process',
                'pages.configuration.formprocess',
                'pages.configuration.organisation',
                'pages.configuration.formorganisation',
                'pages.configuration.systemtasks',

                'pages.report.index',

                'pages.scribble.index',

                'pages.contacts.index',
                'pages.contacts.form',
                'pages.contacts.create',

                'pages.gcontacts.index',
                'pages.gcontacts.form',
                'pages.gcontacts.create'
            ],
            'App\Http\View\Composers\CommonComposer'
        );
    }
}
