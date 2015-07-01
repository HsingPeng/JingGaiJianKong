package cn.edu.njupt.jiankongreceiver;

import android.app.ProgressDialog;
import android.app.Service;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Handler;
import android.os.IBinder;

import java.io.FileOutputStream;
import java.util.Date;
import java.util.List;

import android.app.Notification;
import android.app.NotificationManager;
import android.app.PendingIntent;
import android.content.Context;
import android.support.v4.app.NotificationCompat;
import android.text.format.DateFormat;
import android.util.Log;

import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.JsonObjectRequest;
import com.android.volley.toolbox.Volley;

import org.json.JSONException;
import org.json.JSONObject;

public class SmsService extends Service {

    private static final String TAG = "SmsService";
    private boolean SERVER_OPEN_FLAG = false;

    //创建一个可重用固定线程数的线程池
    //ExecutorService pool = Executors.newFixedThreadPool(CONSTANT.THREAD_POOL_SIZE);

    RequestQueue mQueue;
    private Handler handler = new Handler();

    @Override
    public void onCreate() {
        // TODO Auto-generated method stub
        super.onCreate();
        mQueue = Volley.newRequestQueue(this);
        get_setting();
    }

    /**
     * 读取数据库，重新发送所有失败的请求
     */
    private void read_DB() {
        DBHelper db = new DBHelper(SmsService.this);
        try {
            List<JSONObject> list = db.getAllRecord();
            for (JSONObject jsonObject:list){
                long _id = jsonObject.getLong(FeedEntry._ID);
                jsonObject.remove(FeedEntry._ID);
                startHTTP(jsonObject,_id);
            }
        } catch (JSONException e) {
            e.printStackTrace();
            show(getString(R.string.read_datebase_fail) + e.getMessage());
        }
    }

    /**
     * 读取设置
     * URL和服务是否开启
     */
    private void get_setting() {
        SharedPreferences sp = this.getSharedPreferences(CONSTANT.SHARED_NAME, MODE_PRIVATE);
        CONSTANT.BASE_URL = sp.getString(CONSTANT.BASE_URL_NAME, CONSTANT.BASE_URL);
        boolean flag_open = sp.getBoolean(CONSTANT.OPEN_FLAG, false) ;
        if(flag_open){
            start_service();
        }else{
            close_service();
        }
    }


    @Override
    public int onStartCommand(Intent intent, int flags, int startId) {
        Log.d(TAG, "get intent");
        go(intent);
        return START_REDELIVER_INTENT;
    }

    private void go(Intent intent) {
        int task = intent.getIntExtra(CONSTANT.TASK, CONSTANT.DEFAULT_TASK);
        switch(task){
            case CONSTANT.DEFAULT_TASK:
                break;
            case CONSTANT.SEND_TASK:
                if(SERVER_OPEN_FLAG){
                    try {
                        send(intent);
                    } catch (Exception e) {
                        e.printStackTrace();
                        show(e.getMessage());
                    }
                }
                break;
            case CONSTANT.SEND_TEST_TASK:
                send_test_task();
                break;
            case CONSTANT.START_TASK:
                start_service();
                break;
            case CONSTANT.CLOSE_TASK:
                close_service();
                break;
        }
    }

    private void close_service() {
        deleteIconToStatusbar();
        SERVER_OPEN_FLAG = false;
        Intent intent1 = new Intent(CONSTANT.MAIN_BROADCAST);
        intent1.putExtra(CONSTANT.TASK, CONSTANT.CLOSE_TASK);
        this.sendBroadcast(intent1);
        this.stopSelf();
    }

    private void start_service() {

        read_DB();

        addToStatusbar(getString(R.string.service_started));
        SERVER_OPEN_FLAG = true;
        Intent intent1 = new Intent(CONSTANT.MAIN_BROADCAST);
        intent1.putExtra(CONSTANT.TASK,CONSTANT.START_TASK);
        this.sendBroadcast(intent1);
    }

    /**
     *测试服务器连接是否正常
     */
    private void send_test_task() {
        try {
            send_TEST_HTTP();
        } catch (JSONException e) {
            e.printStackTrace();
            show(e.getMessage());
        }
    }

    /**
     * 发送数据
     *  2#44			报警代码2 倾斜角度44度
     *  1#3.56#0		心跳代码1 电池电压3.56 倾斜角度0
     *  时间样本 2015-06-15 23:39:08
     * @param intent
     */
    private void send(Intent intent) throws JSONException {
        String sender = intent.getStringExtra(CONSTANT.SMS_SENDER);
        String content = intent.getStringExtra(CONSTANT.SMS_CONTENT);
        long send_time = intent.getLongExtra(CONSTANT.SMS_TIME, 0);
        String time = DateFormat.format("yyyy-MM-dd kk:mm:ss", send_time).toString() ;

        addToStatusbar("收到:" + DateFormat.format("yyyy-MM-dd kk:mm:ss", send_time) + "->" + sender + content);
        String data = "\n"+"来自："+sender+"\n"+"内容："+content+"\n"+"时间："+ time+"\n";
        show(data);

        String number;
        if(sender.startsWith("+86")){
            number = sender.substring(3);
        }else{
            number = sender ;
        }

        String[] info= content.split("#");
        int type = Integer.parseInt(info[0]);
        float volt = 0;
        int angle;
        if(type == 1){
            volt = Float.parseFloat(info[1]);
            angle = Integer.parseInt(info[2]);
        }else if(type == 2){
            angle = Integer.parseInt(info[1]);
        }else{
            return;
        }

        sendHTTP(type,number,angle,time,volt);

    }

    /**
     * 发送测试请求
     * @throws JSONException
     */
    private void send_TEST_HTTP() throws JSONException {

        JSONObject jsonObject = new JSONObject();

        jsonObject.put(CONSTANT.TEST, CONSTANT.TEST_FLAG);      //测试标志

        JsonObjectRequest jsonObjectRequest = new JsonObjectRequest(CONSTANT.BASE_URL+CONSTANT.SEND_URL,jsonObject ,
                new Response.Listener<JSONObject>() {
                    @Override
                    public void onResponse(JSONObject response) {
                        Log.d(TAG, response.toString());
                        String data = response.optString(CONSTANT.DATA);
                        if(data.equals(CONSTANT.SUCCESS)){
                            show(getString(R.string.test_server_success)+"-发送日期->"+DateFormat.format("yyyy-MM-dd kk:mm:ss", new Date()));
                        }else{
                            show(getString(R.string.test_server_fail)+"-发送日期->"+DateFormat.format("yyyy-MM-dd kk:mm:ss", new Date()));
                        }
                    }
                }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError error) {
                Log.e(TAG, error.getMessage(), error);
                show(getString(R.string.connect_fail) + "-发送日期->" + DateFormat.format("yyyy-MM-dd kk:mm:ss", new Date()) + "->" + DateFormat.format("yyyy-MM-dd kk:mm:ss", new Date()));
            }
        });

        mQueue.add(jsonObjectRequest);      //执行

    }

    /**
     * 正常发送请求
     * @param type
     * @param number
     * @param angle
     * @param time
     * @param volt
     * @throws JSONException
     */
    private void sendHTTP(final int type,final String number,final int angle, final String time,final float volt) throws JSONException {

        JSONObject jsonObject = new JSONObject();

            jsonObject.put(CONSTANT.TYPE,type+"");
            jsonObject.put(CONSTANT.ANGLE,angle+"");
            jsonObject.put(CONSTANT.NUMBER,number+"");
            jsonObject.put(CONSTANT.TIME,time+"");
            jsonObject.put(CONSTANT.VOLT, volt + "");

        //先把数据存入数据库
        DBHelper db = new DBHelper(this);
        long _id = db.putRecord(type+"",number+"",angle+"",time+"",volt + "");

        startHTTP(jsonObject, _id);

    }

    /**
     * 加入发送队列
     * @param jsonObject
     * @param _id
     */
    private void startHTTP(final JSONObject jsonObject,final long _id) {
        JsonObjectRequest jsonObjectRequest = new JsonObjectRequest(CONSTANT.BASE_URL+CONSTANT.SEND_URL,jsonObject ,
                new Response.Listener<JSONObject>() {
                    @Override
                    public void onResponse(JSONObject response) {
                        Log.d(TAG, response.toString());
                        String data = response.optString(CONSTANT.DATA);

                        //删除数据库里的内容
                        DBHelper db = new DBHelper(SmsService.this);
                        db.deleteRecord(_id);

                        if(data.equals(CONSTANT.SUCCESS)){
                            show(getString(R.string.send_success) + "-发送日期->" + DateFormat.format("yyyy-MM-dd kk:mm:ss", new Date()) + "-number->" + jsonObject.optString(CONSTANT.NUMBER) + "-数据日期->" + jsonObject.optString(CONSTANT.TIME));
                        }else{
                            show(getString(R.string.send_fail) + "-发送日期->" + DateFormat.format("yyyy-MM-dd kk:mm:ss", new Date()) + "-data->" + data + "-号码->" + jsonObject.optString(CONSTANT.NUMBER) + "-数据日期->" + jsonObject.optString(CONSTANT.TIME));
                        }
                    }
                }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError error) {
                Log.e(TAG, error.getMessage(), error);
                //连接失败，准备再次发送
                handler.postDelayed(new Runnable() {
                    @Override
                    public void run() {
                        DBHelper db = new DBHelper(SmsService.this);
                        try {
                            startHTTP(db.getRecord(_id), _id);
                        } catch (JSONException e) {
                            e.printStackTrace();
                            show(getString(R.string.read_datebase_fail) + e.getMessage());
                        }
                    }
                }, CONSTANT.RETRY_TIME);
                show(getString(R.string.connect_fail) + "-发送日期->" + DateFormat.format("yyyy-MM-dd kk:mm:ss", new Date()) + "-号码->" + jsonObject.optString(CONSTANT.NUMBER) + "-数据日期->" + jsonObject.optString(CONSTANT.TIME));
            }
        });

        mQueue.add(jsonObjectRequest);      //执行
    }


    /**
     * 显示数据并且写入日志文件
     */
    private void show(String data){

        data+="\n";

        Intent intent1 = new Intent(CONSTANT.MAIN_BROADCAST);
        intent1.putExtra(CONSTANT.TASK,CONSTANT.SHOW_TEXT_TASK);
        intent1.putExtra(CONSTANT.DATA, data);
        this.sendBroadcast(intent1);
        Log.d(TAG, "send broadcast");
        writeFileData(data);
    }

    //向指定的文件中写入日志
    public void writeFileData(String message){
        try {
            FileOutputStream fout = this.openFileOutput(CONSTANT.LOG_FILE_NAME, Context.MODE_APPEND);//获得FileOutputStream
            //将要写入的字符串转换为byte数组
            byte[]  bytes = message.getBytes();
            fout.write(bytes);//将byte数组写入文件
            fout.close();//关闭文件输出流
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    @Override
    public void onDestroy() {
        // TODO Auto-generated method stub
        super.onDestroy();
        deleteIconToStatusbar();
        mQueue.stop();
    }

    /*
     * 状态栏更新
     */
    private void addToStatusbar(String text){
        NotificationManager nm = (NotificationManager) getSystemService(Context.NOTIFICATION_SERVICE);
        NotificationCompat.Builder mBuilder = new NotificationCompat.Builder(this);
        mBuilder.setSmallIcon(R.mipmap.ic_launcher)
                .setOngoing(true)
                .setTicker(getString(R.string.service_started))
                .setContentText(text)
                .setContentTitle(getString(R.string.service_running));

        Notification n = new Notification();
        //常驻状态栏的图标
        n.icon = R.mipmap.ic_launcher;
        // 将此通知放到通知栏的"Ongoing"即"正在运行"组中
        n.flags |= Notification.FLAG_ONGOING_EVENT;
        // 表明在点击了通知栏中的"清除通知"后，此通知不清除， 经常与FLAG_ONGOING_EVENT一起使用
        n.flags |= Notification.FLAG_NO_CLEAR;
        Intent intent = new Intent(this,MainActivity.class);
        PendingIntent pi = PendingIntent.getActivity(this, 0, intent, 0);
        mBuilder.setContentIntent(pi);

        //n.contentIntent = pi;
        //n.setLatestEventInfo(this, "程序正在运行", text, pi);
        nm.notify(CONSTANT.NOTIFICATION_ID, mBuilder.build());
    }

    private void deleteIconToStatusbar(){
        NotificationManager nm = (NotificationManager) getSystemService(Context.NOTIFICATION_SERVICE);
        nm.cancel(CONSTANT.NOTIFICATION_ID);
    }

    @Override
    public IBinder onBind(Intent intent) {
        // TODO: Return the communication channel to the service.
        throw new UnsupportedOperationException("Not yet implemented");
    }
}
