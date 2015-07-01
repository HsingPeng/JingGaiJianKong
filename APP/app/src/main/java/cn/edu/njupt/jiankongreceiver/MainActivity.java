package cn.edu.njupt.jiankongreceiver;

import android.app.AlertDialog;
import android.app.ProgressDialog;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.IntentFilter;
import android.content.SharedPreferences;
import android.graphics.Color;
import android.os.Build;
import android.os.Handler;
import android.support.v7.app.ActionBarActivity;
import android.os.Bundle;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.ScrollView;
import android.widget.TextView;

import org.apache.http.util.EncodingUtils;

import java.io.FileInputStream;
import java.util.Timer;
import java.util.TimerTask;


public class MainActivity extends ActionBarActivity {

    //常量，为编码格式
    public static final String ENCODING = "UTF-8";

    protected static final String TAG = "MainActivity";
    private BroadcastReceiver receiver;
    private TextView text_show;
    private Button button_change;
    private TextView text_status;

    private Handler handler = new Handler();
    private ProgressDialog pDialog ;
    private ScrollView scrollView1;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        pDialog = new ProgressDialog(this);
        pDialog.setMessage(getString(R.string.loading));

        initView();

        receiver = new BroadcastReceiver() {

            @Override
            public void onReceive(Context context, Intent intent) {
                int task = intent.getIntExtra(CONSTANT.TASK, CONSTANT.DEFAULT_TASK);
                switch(task){
                    case CONSTANT.DEFAULT_TASK:
                        break;
                    case CONSTANT.SHOW_TEXT_TASK:
                        if(pDialog!=null) {
                            pDialog.cancel();
                        }
                        String data = intent.getStringExtra(CONSTANT.DATA);
                        showText(data);
                        Log.d(TAG, "get broadcast->" + data);
                        break;
                    case CONSTANT.START_TASK:
                        button_change.setClickable(true);
                        change_open_status(true);
                        break;
                    case CONSTANT.CLOSE_TASK:
                        button_change.setClickable(true);
                        change_open_status(false);
                        break;
                }

            }
        };

        get_setting();

        showText("\n"+readFileData());

    }

    /**
     * 读取设置
     */
    private void get_setting() {
        SharedPreferences sp = this.getSharedPreferences(CONSTANT.SHARED_NAME, MODE_PRIVATE);
        CONSTANT.BASE_URL = sp.getString(CONSTANT.BASE_URL_NAME,CONSTANT.BASE_URL);
        boolean flag_open = sp.getBoolean(CONSTANT.OPEN_FLAG, false) ;
        change_open_status(flag_open);
        if(flag_open){
            startService(new Intent(this,SmsService.class));
        }
    }

    //打开指定文件，读取其数据，返回字符串对象
    public String readFileData(){
        String result="";
        try {
            FileInputStream fin = openFileInput(CONSTANT.LOG_FILE_NAME);
            //获取文件长度
            int lenght = fin.available();
            byte[] buffer = new byte[lenght];
            fin.read(buffer);
            //将byte数组转换成指定格式的字符串
            result = EncodingUtils.getString(buffer, ENCODING);
        } catch (Exception e) {
            e.printStackTrace();
        }
        return result;
    }

    private void initView() {
        text_show = (TextView)findViewById(R.id.textView_show);
        text_status = (TextView)findViewById(R.id.textView_status);
        button_change = (Button)findViewById(R.id.button_change);

        button_change.setOnClickListener(new View.OnClickListener() {

            @Override
            public void onClick(View v) {
                SharedPreferences sp = MainActivity.this.getSharedPreferences(CONSTANT.SHARED_NAME, MODE_PRIVATE);
                boolean flag_open = sp.getBoolean(CONSTANT.OPEN_FLAG, false);

                if (flag_open) {
                    Intent intent1 = new Intent(MainActivity.this, SmsService.class);
                    intent1.putExtra(CONSTANT.TASK, CONSTANT.CLOSE_TASK);
                    MainActivity.this.startService(intent1);
                } else {
                    Intent intent1 = new Intent(MainActivity.this, SmsService.class);
                    intent1.putExtra(CONSTANT.TASK, CONSTANT.START_TASK);
                    MainActivity.this.startService(intent1);
                }

                button_change.setClickable(false);
                handler.postDelayed(new Runnable() {
                    @Override
                    public void run() {
                        button_change.setClickable(true);
                    }
                },1000);

            }
        });

        scrollView1 = (ScrollView)findViewById(R.id.scrollView1);

    }



    private void change_open_status(boolean flag_open) {
        SharedPreferences sp = MainActivity.this.getSharedPreferences(CONSTANT.SHARED_NAME, MODE_PRIVATE);
        SharedPreferences.Editor editor = sp.edit();        //一定要取出，否则不是编辑状态，无法写入
        if(flag_open){
            editor.putBoolean(CONSTANT.OPEN_FLAG, true);
            editor.commit();

            text_status.setText(R.string.service_started);
            text_status.setTextColor(Color.BLUE);
            button_change.setText(R.string.click_stop);
            startService(new Intent(this,SmsService.class));
        }else{
            editor.putBoolean(CONSTANT.OPEN_FLAG, false);
            editor.commit();

            text_status.setText(R.string.service_stoped);
            text_status.setTextColor(Color.RED);
            button_change.setText(R.string.click_open);
            stopService(new Intent(this,SmsService.class));
        }


    }


    /**
     * 将文字显示在界面的TextView上
     * @param data
     */
    protected void showText(String data) {
        // TODO Auto-generated method stub
        text_show.setText(text_show.getText() + data);
        scrollView1.fullScroll(ScrollView.FOCUS_DOWN);      //滑动到底部
    }



    @Override
    protected void onStart() {
        super.onStart();
        registerReceiver(receiver, new IntentFilter(CONSTANT.MAIN_BROADCAST));
    }



    @Override
    protected void onStop() {
        super.onStop();
        unregisterReceiver(receiver);
    }



    @Override
    protected void onDestroy() {
        // TODO Auto-generated method stub
        super.onDestroy();
    }



    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        switch(item.getItemId()){
            case R.id.action_clear:
                openClearDialog();
                break;
            case R.id.action_settings:
                openSettingDialog();
                break;
            case R.id.action_exit:
                this.finish();
                break;
            case R.id.action_test_service:
                test_service();
                break;
        }
        return true;
    }

    /**
     * 打开设置弹窗
     */
    private void openSettingDialog() {
        LinearLayout layout = new LinearLayout(this);
        layout.setOrientation(LinearLayout.VERTICAL);
        layout.setPadding(32, 32, 32, 32);
        if (Build.VERSION.SDK_INT < Build.VERSION_CODES.HONEYCOMB) {
            //TODO:如果当前版本小于HONEYCOMB版本，即3.0版本
            layout.setBackgroundColor(Color.WHITE);
        }
        TextView text_base_URL = new TextView(this);
        text_base_URL.setText(R.string.set_base_URL);
        final EditText edit_base_URL = new EditText(this);
        edit_base_URL.setText(CONSTANT.BASE_URL);
        edit_base_URL.setHint(CONSTANT.DEFAULT_BASE_URL);

        layout.addView(text_base_URL);
        layout.addView(edit_base_URL);

        AlertDialog.Builder builder = new AlertDialog.Builder(this);
        builder.setTitle(R.string.setting);
        builder.setView(layout);

        // Add the buttons
        builder.setPositiveButton(R.string.cancel, new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int id) {

            }
        });
        builder.setNegativeButton(R.string.ok, new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int id) {
                set_BASE_URL(edit_base_URL.getText().toString());
            }
        });

        // Create the AlertDialog
        AlertDialog dialog = builder.create();
        dialog.show();

    }

    /**
     * 存储服务器地址
     * @param base_url
     */
    private void set_BASE_URL(String base_url) {
        SharedPreferences sp = MainActivity.this.getSharedPreferences(CONSTANT.SHARED_NAME, MODE_PRIVATE);
        SharedPreferences.Editor  editor  =  sp.edit();		//一定要取出，否则不是编辑状态，无法写入
        if(base_url.equals("")){
            editor.putString(CONSTANT.BASE_URL_NAME, CONSTANT.BASE_URL);
            editor.commit();
        }else{
            CONSTANT.BASE_URL = base_url;
            editor.putString(CONSTANT.BASE_URL_NAME, base_url);
            editor.commit();
        }
    }

    /**
     * 测试服务器连接
     */
    private void test_service() {
        pDialog.show();
        Intent intent1 = new Intent(MainActivity.this, SmsService.class);
        intent1.putExtra(CONSTANT.TASK, CONSTANT.SEND_TEST_TASK);
        MainActivity.this.startService(intent1);
    }

    /**
     * 打开确认清理日志的弹窗
     */
    private void openClearDialog() {
        AlertDialog.Builder builder = new AlertDialog.Builder(this);
        builder.setMessage(R.string.confirm_delete_log_file);
        // Add the buttons
        builder.setPositiveButton(R.string.cancel, new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int id) {

            }
        });
        builder.setNegativeButton(R.string.ok, new DialogInterface.OnClickListener() {
            public void onClick(DialogInterface dialog, int id) {
                deleteFile(CONSTANT.LOG_FILE_NAME);
                text_show.setText(R.string.show_log_begin);
            }
        });


        // Create the AlertDialog
        AlertDialog dialog = builder.create();
        dialog.show();
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.menu_main, menu);
        return true;
    }

}
