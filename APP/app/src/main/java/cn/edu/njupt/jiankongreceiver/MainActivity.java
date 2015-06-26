package cn.edu.njupt.jiankongreceiver;

import android.app.AlertDialog;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.IntentFilter;
import android.content.SharedPreferences;
import android.graphics.Color;
import android.support.v7.app.ActionBarActivity;
import android.os.Bundle;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.Button;
import android.widget.TextView;

import org.apache.http.util.EncodingUtils;

import java.io.FileInputStream;


public class MainActivity extends ActionBarActivity {

    //常量，为编码格式
    public static final String ENCODING = "UTF-8";

    protected static final String TAG = "MainActivity";
    private BroadcastReceiver receiver;
    private TextView text_show;
    private Button button_change;
    private TextView text_status;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        initView();

        receiver = new BroadcastReceiver() {

            @Override
            public void onReceive(Context context, Intent intent) {
                // TODO Auto-generated method stub
                String data = intent.getStringExtra(CONSTANT.DATA);
                showText(data);
                Log.d(TAG, "get broadcast->" + data);
            }
        };

        SharedPreferences sp = getSharedPreferences(CONSTANT.SHARED_NAME, MODE_PRIVATE);
        boolean flag_open = sp.getBoolean(CONSTANT.OPEN_FLAG, false) ;
        change_open_status(flag_open);
        if(flag_open){
            startService(new Intent(this,SmsService.class));
        }

        showText(readFileData());

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
                boolean flag_open = sp.getBoolean(CONSTANT.OPEN_FLAG, false) ;
                SharedPreferences.Editor  editor  =  sp.edit();		//一定要取出，否则不是编辑状态，无法写入
                if(flag_open){
                    editor.putBoolean(CONSTANT.OPEN_FLAG, false);
                    editor.commit();
                    change_open_status(false);
                }else{

                    editor.putBoolean(CONSTANT.OPEN_FLAG, true);
                    editor.commit();
                    change_open_status(true);
                }
            }
        });


    }



    private void change_open_status(boolean flag_open) {
        if(flag_open){
            text_status.setText(R.string.service_started);
            text_status.setTextColor(Color.BLUE);
            button_change.setText(R.string.click_stop);
            startService(new Intent(this,SmsService.class));
        }else{
            text_status.setText(R.string.service_stoped);
            text_status.setTextColor(Color.RED);
            button_change.setText(R.string.click_open);
            stopService(new Intent(this,SmsService.class));
        }


    }



    protected void showText(String data) {
        // TODO Auto-generated method stub
        text_show.setText(text_show.getText() + data);
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
                openDialog();
                break;
            case R.id.action_settings:
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

    private void test_service() {

    }

    private void openDialog() {
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
