float atan2( float x, float y ) {
    if( y == 0.0)
        if( x >= 0.0 )
            return 0.0;
        else
            return 3.14159;

    return 2.0 * atan((sqrt(x*x + y*y) - x) / y);
}

float heading( float x, float y ) {
    float val;

    if( y == 0.0 )
        if( x > 0.0 )
            return 90.0;
        else if( x == 0.0 )
            return 0.0;
        else
            return 270.0;

    val = atan2(x,y) * 180.0 / 3.14159;

    if(val > 90.0)
        return 450.0 - val;
    else if(val > 0.0)
        return 90.0 - val;
    else if(val < -90.0)
        return (0.0 - val) + 90.0;
    else
        return 90.0 - val;
}
