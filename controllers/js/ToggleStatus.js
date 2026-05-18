function toggle(id)
{
    var x=new XMLHttpRequest();
    x.onreadystatechange=function()
    {
        if(this.readyState==4)
            {
                if(this.status==200)
                    {
                        var r=JSON.parse(this.responseText);

                        if(r.success)
                            {
                                document.getElementById('s'+id).innerHTML=r.status;

                                if(r.status=='active')
                                    {
                                        document.getElementById('s'+id).setAttribute('color','green');
                                    }
                                else
                                    {
                                        document.getElementById('s'+id).setAttribute('color','red');
                                    }
                            }
                        else
                            {
                                alert(r.message);
                            }
                    }
                else
                    {
                        alert('Ajax Error');
                    }
            }
    };
    x.open('POST','../Controller/JobStatusController.php',true);
    x.setRequestHeader('Content-type','application/x-www-form-urlencoded');
    x.send('id='+id);
}
