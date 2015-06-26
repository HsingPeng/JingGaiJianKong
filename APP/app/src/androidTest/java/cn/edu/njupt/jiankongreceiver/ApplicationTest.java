package cn.edu.njupt.jiankongreceiver;

import android.app.Application;
import android.content.Intent;
import android.test.ApplicationTestCase;
import android.util.Log;

import com.android.volley.AuthFailureError;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.JsonObjectRequest;
import com.android.volley.toolbox.Volley;

import org.json.JSONException;
import org.json.JSONObject;

import java.util.HashMap;
import java.util.Map;

/**
 * <a href="http://d.android.com/tools/testing/testing_android.html">Testing Fundamentals</a>
 */
public class ApplicationTest extends ApplicationTestCase<Application> {
    public ApplicationTest() {
        super(Application.class);
    }

    public void test1(){


        Intent intent1 = new Intent(getContext(), SmsService.class);
        intent1.putExtra(CONSTANT.SMS_SENDER, "15555215556");
        intent1.putExtra(CONSTANT.TASK, CONSTANT.SEND_TASK);
        intent1.putExtra(CONSTANT.SMS_CONTENT, "1#3.15#0");
        intent1.putExtra(CONSTANT.SMS_TIME, "2015-06-15 23:43:54");
        getContext().startService(intent1);
    }

    public void test2(){

        RequestQueue mQueue = Volley.newRequestQueue(getContext());

        JSONObject jsonObject = new JSONObject();
        final int type = 1 ;
        final int angle = 0 ;
        final String number = "15555215556" ;
        final String time = "2015-06-15 23:43:54" ;
        final float volt = 3.15f ;

        try {

            if(type==2){        //报警

                    jsonObject.put(CONSTANT.TYPE,type);

                jsonObject.put(CONSTANT.ANGLE,angle);
                jsonObject.put(CONSTANT.NUMBER,number);
                jsonObject.put(CONSTANT.TIME,time);
            }else if(type==1){      //心跳
                jsonObject.put(CONSTANT.ANGLE,angle);
                jsonObject.put(CONSTANT.NUMBER,number);
                jsonObject.put(CONSTANT.TIME,time);
                jsonObject.put(CONSTANT.VOLT, volt);
                jsonObject.put(CONSTANT.ANGLE,angle);
            }

        } catch (JSONException e) {
            e.printStackTrace();
        }

        JsonObjectRequest jsonObjectRequest = new JsonObjectRequest("http://192.168.1.105/JianKong/home.php?m=Home&c=UpDate&a=update",null ,
                new Response.Listener<JSONObject>() {
                    @Override
                    public void onResponse(JSONObject response) {
                        Log.d("TAG", response.toString());
                        String data = response.optString(CONSTANT.DATA);
                        if(data.equals(CONSTANT.SUCCESS)){

                        }else{

                        }
                    }
                }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError error) {
                Log.e("TAG", error.getMessage(), error);

            }
        }){
            @Override
            public Map<String, String> getHeaders() throws AuthFailureError {
                Map<String, String> map = new HashMap<String, String>();
                if(type==2){        //报警

                    map.put(CONSTANT.TYPE,""+type);

                    map.put(CONSTANT.ANGLE,""+angle);
                    map.put(CONSTANT.NUMBER,number);
                    map.put(CONSTANT.TIME,time);
                }else if(type==1){      //心跳
                    map.put(CONSTANT.ANGLE,""+angle);
                    map.put(CONSTANT.NUMBER,number);
                    map.put(CONSTANT.TIME,time);
                    map.put(CONSTANT.VOLT, ""+volt);
                    map.put(CONSTANT.ANGLE,""+angle);
                }
                return map;
            }
        };
        mQueue.add(jsonObjectRequest);      //执行
    }

}