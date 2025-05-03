<?php
namespace App\Http\Controllers\Feedback;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;


class FeedbackController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Main/Feedback');
    }
}
