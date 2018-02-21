<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Analytics;
use Carbon\Carbon;
use App\User;
use Cache;
use Spatie\Analytics\Period;
use App\Question;
use App\Answer;
use App\Donation;
use App\Progress;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Auth;
class AdminController extends Controller
{
  public function index(){
      return view('realtime');
  }

  public function raise()
  {
    $donators = Donation::latest()->where('status', '=', 100)->take(5)->get();
    $fame = Donation::where([['amount1', '>=', 100], ['status', '=', 100]])->orderBy('amount1', 'desc')->get();
    $backers = Donation::where([['status', '=', 100]])->count();
    $raised = Progress::first();

    return view('admin.raise.index', ['donations' => $donators, 'fame' => $fame, 'backers' => $backers, 'raised' => $raised->USD]);
  }

  public function fame()
  {
    $fame = Donation::where('status', 100)->selectRaw('sum(amount1) as amount1, userid')->groupBy('userid')->orderBy('amount1', 'desc')->get();

    return view('admin.fame.index', ['fame' => $fame]);
  }

  // Users registered total
  public function getUserCount()
  {
    $count = Cache::remember('userCount', 15, function() {
        return count(DB::table('users')->select('id')->get());
    });

    return $count;

  }

  public function impersonate($username)
  {
    $user = User::where('username', $username)->first();
    Auth::user()->impersonate($user);
  }

  public function unImpersonate()
  {
    Auth::user()->leaveImpersonation();
  }

  // Users registered today
  public function getUserCountToday()
  {
    $count = Cache::remember('userCountToday', 15, function() {
        return count(DB::table('users')->select('id')->whereDate('created_at', DB::raw('CURDATE()'))->get());
    });

    return $count;
  }

  // Gets amount of users for a specific time period and groups them by day
  public function getUsers($days)
  {
    if($days != "today")
    {
      $chart = Cache::remember('userChart:'.$days, ($days * 24 * 60), function()
      {
        $day = Carbon::today()->subDays(7);
        $result = DB::select(DB::raw("select count(id) as count, DATE_FORMAT(created_at, '%Y-%m-%d') as created_at from users where created_at >= '$day' group by DATE_FORMAT(created_at, '%Y-%m-%d')"));
        $days = array();
        $x= 0;
        foreach ($result as $user){
           $days[$user->created_at] = $user->count;
        }
        return $days;
      });
     return $chart;
   } else {
     $today = Cache::remember('userChartToday', 60, function(){
       $day = Carbon::today()->subHours(5);
       $day = date('Y-m-d H', strtotime($day));
       $result = DB::select(DB::raw("select count(id) as count, DATE_FORMAT(created_at, '%Y-%m-%d %H') as created_at from users where created_at >= '$day' group by DATE_FORMAT(created_at, '%Y-%m-%d %H')"));
       $days = array();
       $x= 0;
       foreach ($result as $user){
          $days[$user->created_at] = $user->count;
       }
      return $days;
    });
    return $today;
   }
  }

  // Get CPU usage
  public function getCpu()
  {
    $loads = sys_getloadavg();
    $core_nums = trim(shell_exec("grep -P '^processor' /proc/cpuinfo|wc -l"));
    $load = round($loads[0]/($core_nums + 1)*100, 2);

    return $load;
  }

  public function show(){

    // Stats
    $users = $this->getUserCount();
    $userstoday = $this->getUserCountToday();
    $online = Analytics::getAnalyticsService()->data_realtime->get('ga:156576021',  'rt:activeVisitors')->totalsForAllResults['rt:activeVisitors'];
    $last24 = Analytics::fetchTotalVisitorsAndPageViews(Period::days(7));
    $userchart = $this->getUsers(7);
    $userchart_today = $this->getUsers('today');
    $load = $this->getCpu();

    $questions = Question::orderBy('id', 'desc')->take(3)->get();
    $answers = Answer::where([['tag', '=', 1], ['userid', '!=', '774'], ['userid', '!=', '1']])->selectRaw('count(id) as amount, userid')->groupBy('userid')->orderBy('amount', 'desc')->take(5)->get();



    return view('admin.index', ['users' => $users, 'today' => $userstoday, 'online' => $online, 'cpu' => $load, 'userchart' => $userchart, 'userstoday' => $userchart_today, 'last24' => $last24, 'questions' => $questions, 'answers' => $answers]);
  }

    public static function getStaff()
    {
      $staff = DB::table('user_group')->where('group_id', '8')->orWhere('group_id', '10')->orWhere('group_id', '6')->orWhere('group_id', '4')->get();
      return $staff;
    }


    public function setAdmin() {

        // The role to give the user
        $role = Role::findByName('admin');

        // The users current primary role NAME.
        $currentrole = Auth::user()->primary_role;

        // Gets the users primary roles Role Model
        if($currentrole != "")
        {
          $user_role = Role::findByName($currentrole);
        }

        // Checks if the users primary role has a lower level than the new role.
        if($currentrole != "")
        {
          if($user_role->level < $role->level)
          {
            Auth::user()->primary_role = $role->name;
            Auth::user()->save();
          }
        } else {
            Auth::user()->primary_role = $role->name;
            Auth::user()->save();
        }
        // Give the user a role.
        $give = Auth::user()->assignRole('admin');
    }




}
