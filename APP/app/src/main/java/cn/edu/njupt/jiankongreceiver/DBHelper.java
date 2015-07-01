package cn.edu.njupt.jiankongreceiver;

import android.content.ContentValues;
import android.content.Context;
import android.database.Cursor;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteOpenHelper;
import android.provider.BaseColumns;

import com.android.volley.toolbox.JsonObjectRequest;

import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.List;

/**
 * Created by DEEP on 2015/6/30.
 */
public class DBHelper extends SQLiteOpenHelper {

    private static final String DATABASE_NAME = "task.db";
    private static final int DATABASE_VERSION = 1;

    private static final String TEXT_TYPE = " TEXT";
    private static final String COMMA_SEP = ",";
    private static final String SQL_CREATE_ENTRIES =
            "CREATE TABLE IF NOT EXISTS " + FeedEntry.TABLE_NAME + " (" +
                    FeedEntry._ID + " INTEGER PRIMARY KEY AUTOINCREMENT," +
                    FeedEntry.COLUMN_NAME_TYPE + TEXT_TYPE + COMMA_SEP +
                    FeedEntry.COLUMN_NAME_ANGLE + TEXT_TYPE + COMMA_SEP +
                    FeedEntry.COLUMN_NAME_NUMBER + TEXT_TYPE + COMMA_SEP +
                    FeedEntry.COLUMN_NAME_TIME + TEXT_TYPE + COMMA_SEP +
                    FeedEntry.COLUMN_NAME_VOLT + TEXT_TYPE +
            " )";
    private static final String SQL_DELETE_ENTRIES =
            "DROP TABLE IF EXISTS " + FeedEntry.TABLE_NAME;

    public DBHelper(Context context) {
        super(context, DATABASE_NAME, null, DATABASE_VERSION);
    }

    @Override
    public void onCreate(SQLiteDatabase db) {
        db.execSQL(SQL_CREATE_ENTRIES);
    }

    @Override
    public void onUpgrade(SQLiteDatabase db, int oldVersion, int newVersion) {
        db.execSQL(SQL_CREATE_ENTRIES);
        onCreate(db);
    }

    /**
     * 获取失败的数据
     * @param _id
     * @return
     * @throws JSONException
     */
    public JSONObject getRecord(long _id) throws JSONException {
        SQLiteDatabase db = this.getReadableDatabase();

        String[] projection = {
                FeedEntry._ID,
                FeedEntry.COLUMN_NAME_TYPE,
                FeedEntry.COLUMN_NAME_ANGLE,
                FeedEntry.COLUMN_NAME_NUMBER,
                FeedEntry.COLUMN_NAME_TIME,
                FeedEntry.COLUMN_NAME_VOLT
            };
        String selection = FeedEntry._ID + "=?";
        String[] selectionArgs = new String[]{String.valueOf(_id)};

        Cursor cursor = db.query(FeedEntry.TABLE_NAME,projection,selection,selectionArgs,null,null,null);
        cursor.moveToFirst();
        String type = cursor.getString(cursor.getColumnIndexOrThrow(FeedEntry.COLUMN_NAME_TYPE));
        String angle = cursor.getString(cursor.getColumnIndexOrThrow(FeedEntry.COLUMN_NAME_ANGLE));
        String number = cursor.getString(cursor.getColumnIndexOrThrow(FeedEntry.COLUMN_NAME_NUMBER));
        String time = cursor.getString(cursor.getColumnIndexOrThrow(FeedEntry.COLUMN_NAME_TIME));
        String volt = cursor.getString(cursor.getColumnIndexOrThrow(FeedEntry.COLUMN_NAME_VOLT));

        JSONObject jsonObject = new JSONObject();

        jsonObject.put(CONSTANT.TYPE,type);
        jsonObject.put(CONSTANT.ANGLE,angle);
        jsonObject.put(CONSTANT.NUMBER,number);
        jsonObject.put(CONSTANT.TIME,time);
        jsonObject.put(CONSTANT.VOLT, volt);

        return jsonObject;
    }

    /**
     * 获取所有失败的数据
     * @return
     * @throws JSONException
     */
    public List<JSONObject> getAllRecord() throws JSONException {
        SQLiteDatabase db = this.getReadableDatabase();

        String[] projection = {
                FeedEntry._ID,
                FeedEntry.COLUMN_NAME_TYPE,
                FeedEntry.COLUMN_NAME_ANGLE,
                FeedEntry.COLUMN_NAME_NUMBER,
                FeedEntry.COLUMN_NAME_TIME,
                FeedEntry.COLUMN_NAME_VOLT
        };

        Cursor cursor = db.query(FeedEntry.TABLE_NAME,projection,null,null,null,null,null);

        List<JSONObject> list = new ArrayList<>();

        while (cursor.moveToNext()) {
            Long _id = cursor.getLong(cursor.getColumnIndexOrThrow(FeedEntry._ID));
            String type = cursor.getString(cursor.getColumnIndexOrThrow(FeedEntry.COLUMN_NAME_TYPE));
            String angle = cursor.getString(cursor.getColumnIndexOrThrow(FeedEntry.COLUMN_NAME_ANGLE));
            String number = cursor.getString(cursor.getColumnIndexOrThrow(FeedEntry.COLUMN_NAME_NUMBER));
            String time = cursor.getString(cursor.getColumnIndexOrThrow(FeedEntry.COLUMN_NAME_TIME));
            String volt = cursor.getString(cursor.getColumnIndexOrThrow(FeedEntry.COLUMN_NAME_VOLT));
            cursor.close();

            JSONObject jsonObject = new JSONObject();

            jsonObject.put(FeedEntry._ID, _id);
            jsonObject.put(CONSTANT.TYPE, type);
            jsonObject.put(CONSTANT.ANGLE, angle);
            jsonObject.put(CONSTANT.NUMBER, number);
            jsonObject.put(CONSTANT.TIME, time);
            jsonObject.put(CONSTANT.VOLT, volt);

            list.add(jsonObject);

        }
        cursor.close();

        return list;
    }

    /**
     * 存入数据库
     * @param type
     * @param number
     * @param angle
     * @param time
     * @param volt
     * @return
     */
    public long putRecord(final String type,final String number,final String angle, final String time,final String volt){
        // Gets the data repository in write mode
        SQLiteDatabase db = this.getWritableDatabase();

        // Create a new map of values, where column names are the keys
        ContentValues values = new ContentValues();
        values.put(FeedEntry.COLUMN_NAME_TYPE, type);
        values.put(FeedEntry.COLUMN_NAME_ANGLE, angle);
        values.put(FeedEntry.COLUMN_NAME_NUMBER, number);
        values.put(FeedEntry.COLUMN_NAME_TIME, time);
        values.put(FeedEntry.COLUMN_NAME_VOLT, volt);

        // Insert the new row, returning the primary key value of the new row
        long newRowId;
        newRowId = db.insert(
                FeedEntry.TABLE_NAME,
                null,
                values);

        return newRowId;
    }

    /**
     * 删除数据
     * @param _id
     */
    public void deleteRecord(long _id){
        SQLiteDatabase db = this.getWritableDatabase();
        // Define 'where' part of query.
        String selection = FeedEntry._ID + "=?";
        // Specify arguments in placeholder order.
        String[] selectionArgs = {String.valueOf(_id)};
        // Issue SQL statement.
        db.delete(FeedEntry.TABLE_NAME, selection, selectionArgs);
    }

}

/* Inner class that defines the table contents */
class FeedEntry implements BaseColumns {
    public static final String TABLE_NAME = "record";
    public static final String COLUMN_NAME_TYPE = "type";
    public static final String COLUMN_NAME_ANGLE = "angle";
    public static final String COLUMN_NAME_NUMBER = "number";
    public static final String COLUMN_NAME_TIME = "time";
    public static final String COLUMN_NAME_VOLT = "volt";
}