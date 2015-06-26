package cn.edu.njupt.jiankongreceiver;

import java.util.Date;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.telephony.SmsMessage;
import android.util.Log;

public class SmsReceiver extends BroadcastReceiver {

    private static final String TAG = "SmsReceiver";

    @Override
    public void onReceive(Context context, Intent intent) {


        // 判断是系统短信；
        if (isOpen(context) && intent.getAction()
                .equals("android.provider.Telephony.SMS_RECEIVED")) {
            // 不再往下传播；
            this.abortBroadcast();
            StringBuffer sb = new StringBuffer();
            String sender = null;
            String content = null;
            Bundle bundle = intent.getExtras();
            if (bundle != null) {
                // 通过pdus获得接收到的所有短信消息，获取短信内容；
                Object[] pdus = (Object[]) bundle.get("pdus");
                // 构建短信对象数组；
                SmsMessage[] mges = new SmsMessage[pdus.length];
                for (int i = 0; i < pdus.length; i++) {
                    // 获取单条短信内容，以pdu格式存,并生成短信对象；
                    mges[i] = SmsMessage.createFromPdu((byte[]) pdus[i]);
                }
                for (SmsMessage mge : mges) {
                    sb.append("短信来自：" + mge.getDisplayOriginatingAddress() + "\n");
                    sb.append("短信内容：" + mge.getMessageBody() + "\n");
                    sb.append("短信时间：" + new Date(mge.getTimestampMillis()).toString());

                    sender = mge.getDisplayOriginatingAddress();// 获取短信的发送者
                    content = mge.getMessageBody();// 获取短信的内容

                    Intent intent1 = new Intent(context, SmsService.class);
                    intent1.putExtra(CONSTANT.SMS_SENDER, sender);
                    intent1.putExtra(CONSTANT.TASK, CONSTANT.SEND_TASK);
                    intent1.putExtra(CONSTANT.SMS_CONTENT, content);
                    intent1.putExtra(CONSTANT.SMS_TIME, mge.getTimestampMillis());
                    context.startService(intent1);
                    Log.d(TAG, "send intent");
                }
                //Toast.makeText(context, sb.toString(), Toast.LENGTH_LONG).show();
            }


        }

    }

    private boolean isOpen(Context context) {
        SharedPreferences sp = context.getSharedPreferences(CONSTANT.SHARED_NAME, context.MODE_PRIVATE);

        return sp.getBoolean(CONSTANT.OPEN_FLAG, false);
    }

}
