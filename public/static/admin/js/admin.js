if (typeof ($.jgrid) != 'undefined' && typeof ($.jgrid) == 'object') {
    // jgrid 初始化
    $.extend($.jgrid.defaults, {
        datatype: 'json',
        mtype: "POST",
        styleUI: 'Bootstrap',
        height: "100%",
        shrinkToFit: true,
        autowidth: true,
        autoScroll: true,
        sortable: false,
        autowidth: true,
        viewrecords: true,
        responsive: true,
        rowNum: 20,                     //每页条数
        rowList: [20, 40, 60, 100],         //自定义每页条数，会覆盖rowNum
        pager: "#dmDataGridPage",            //是否要显示总记录数
        emptyrecords: '没有查询到数据',				//当数据为0时，显示的信息
        // rownumbers:true,                //是否显示行号
        multiselect: false,				//是否显示多选checkbox
        ajaxGridOptions: {
            // beforeSend : function(xhr){
            //     xhr.setRequestHeader('isAjax','1');
            // },
            // statusCode:{
            //     401:function (XMLHttpRequest, textStatus, errorThrown) {
            //         //登录过期，跳转首页重新登录
            //         if(401 === jqXHR.status){
            //             window.location.href = window.location.href;
            //         }
            //     }
            // }
        },
        jsonReader: {
            root: "rows",
            page: "page",
            total: "pages",
            records: "total",
        },
        treedatatype: "json",
        prmNames: {
            page: "page",
            rows: "rp",
            sort: "sortname",
            order: "sortorder",
            search: "_search",
            nd: "_tnd",
            id: "id",
            oper: "oper",
            editoper: "edit",
            addoper: "add",
            deloper: "del",
            subgridid: "id",
            npage: null,
            totalrows: "totalrows"
        }
    });
}
// 查询
function searchGrid(grid,formId){
    var postData = $(grid).jqGrid("getGridParam", "postData");
    $.each(postData, function (k, v) {
        delete postData[k];
    });
    $(grid).setGridParam({postData:getQueryParams(formId)}).trigger("reloadGrid");
}
// 参数处理
function getQueryParams(formId) {
    var params = $(formId).serializeArray();
    var par = {};
    $.each(params, function (k, v) {
        if (v.value) {
            par[v.name] = v.value;
        }
    });
    return par;
}
// 二次封装扩展
var DM = {
    open:function(title,url,height="500px"){
		var index = top.layer.open({
		    type: 2,
		    title: title||'',
		    btn: ['提交','关闭'],
		    maxmin: false,
		    // anim:false,
		    // isOutAnim:false,
		    area: ['800px', height],
		    content: url,
		    yes: function(index, layero){
				var form = top.layer.getChildFrame('#dosubmit',index);
                form.click();
            },
            end:function(){   //新增end层
                $("#dmDataGrid").trigger("reloadGrid");
            }
		});
	},
    // ajax post请求
	post: function (url,data,callback,iframe){
		var loadIndex = top.layer.load(1, {shade: false});
	    $.ajax({
            type : "POST",
            url:url,
            data : data,
            dataType: 'json',
            cache: false,
            success : function(ret, status) {
                top.layer.close(loadIndex);
            	if(ret){
            		if(ret.code == 1){
                        DM.msg(ret.msg || "操作成功");
                        if (!iframe) {
                            top.layer.closeAll('iframe');
                        }
                        $("#dmDataGrid").trigger("reloadGrid");
            		}else{
            			DM.msg(ret.msg||"操作失败");
            		}
	                if (callback){
	    				callback(ret);
                    }
            	}else{
            		DM.msg(ret.msg||"操作失败");
                }
            },
            error: function(err){
            	top.layer.close(loadIndex);
            	DM.msg('操作失败');
            }
        });
    },
    delete: function (title, url, data, callback) {
        top.layer.confirm(title || '确定要执行当前操作吗？', {
            btn: ['确定', '取消'] //按钮
        }, function (index) {
            top.layer.close(index);
            DM.post(url, data, callback);
        }, function () {
            // callback(-1);
        });
    },
    msg: function(msg){
        top.layer.msg(msg,{time:1500});
    }
}