<?php

namespace App\Services;

use App\Http\Resources\V1\Admin\WorkerResource;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminService
{


    public function getWorkers(User $user)
    {
        $workers = $user->pharmacy->staff()->role('worker')->get();
        $whitelist = $user->pharmacy->whitelist->pluck('email');

        foreach ($workers as $worker) {
            $isWhitelisted = $whitelist->contains($worker->email);

            if ($isWhitelisted) {
                $worker->status = $worker->isOnline() ? 'online' : 'offline';
            } else {
                $worker->status = 'disabled';
            }
        }

        return WorkerResource::collection($workers);
    }

    public function enableWorker(User $user, $worker_id)
    {
        $worker = $user->pharmacy->staff()->find($worker_id);
        if (!$worker) {
            throw new NotFoundHttpException();
        }

        DB::table('pharmacy_staff_whitelist')->updateOrInsert([
            'pharmacy_id' => $worker->pharmacy_id,
            'email' => $worker->email
        ]);

        return;
    }

    public function disableWorker(User $user, $worker_id)
    {
        /** @var \App\Models\User $worker */
        $worker = $user->pharmacy->staff()->role('worker')->find($worker_id); //! DATABASE query instead of memory filer idk UwU
        if (!$worker) {
            throw new NotFoundHttpException();
        }
        $worker->tokens()->delete();
        DB::table('pharmacy_staff_whitelist')->where('email', '=', $worker->email)->delete();
        return;
    }
}
