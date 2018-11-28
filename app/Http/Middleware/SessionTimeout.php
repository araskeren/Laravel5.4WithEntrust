<?php

namespace App\Http\Middleware;

use Closure;

class SessionTimeout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    protected $session;
    //86400 detik = 1 Hari
    protected $timeout=86400;
    
    public function __construct(Store $session){
        $this->session=$session;
    }
    public function handle($request, Closure $next)
    {
        if(!$this->session->has('lastActivityTime'))
            $this->session->put('lastActivityTime',time());
        elseif(time() - $this->session->get('lastActivityTime') > $this->getTimeOut()){
            $this->session->forget('lastActivityTime');
            $user = Auth::user();
            $user->session_id = null;
            $user->last_logout = carbon::now();
            $user->save();
            Auth::logout();
            
            return redirect('/login')->withErrors(['Selama 30 Menit kamu diem2 bae, lagi ngopi ya?. login ulang sana']);
        }
        $this->session->put('lastActivityTime',time());
        return $next($request);
    }

    protected function getTimeOut()
    {
        return (env('TIMEOUT')) ?: $this->timeout;
    }
}
