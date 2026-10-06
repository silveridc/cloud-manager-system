{extend name="base" /}

{block name="head"}
<link href='{$static}/css/json.css' rel='stylesheet' type='text/css'>
<script src="{$static}/js/json.js" type="text/javascript"></script>
{/block}
{block name="main"}
<div class="container">
    <div class="jumbotron">
        <p class="bg-success" style="font-size: 18px;">文档地址：{$root}/doc?name={$doc['name']}</p>
        <h2>接口：{$doc.title|default="请设置title注释"}</h2>
        <p>接口地址：{$doc.url|default="请设置url注释"} <span class="label label-success">{$doc.method|default='GET'}</span></p>
        <p class="text-primary">{$doc.title|default="请设置title注释"} -- {$doc.author|default="请设置author注释"}</p>
        <br/>
        <p><strong>{$doc.description|default=""}</strong></p><br/>

        <ul id="myTab" class="nav nav-tabs">
            <li class="active"><a href="#info" data-toggle="tab">接口信息</a></li>
        </ul>
        <div class="tab-content">
            <!--info-->
            <div class="tab-pane fade in active" id="info">
                {if condition="isset($doc.header) &&  !empty($doc.header)"}
                <h3>请求Headers</h3>
                <table class="table table-striped" >
                    <tr><th>名称</th><th>是否必须</th><th>默认值</th><th>说明</th></tr>
                    {volist name="doc.header" id="head"}
                    <tr>
                        <td>{$head.name|default="-"}</td>
                        <td>{if condition="$head.require eq 1"}必填{else/}非必填{/if}</td>
                        <td>{$head.default|default="-"}</td>
                        <td>{$head.desc|default="-"}</td>
                    </tr>
                    {/volist}
                </table>
                <br>
                {/if}
                {if condition="isset($doc.param)"}
                <h3>接口参数</h3>
                <table class="table table-striped" >
                    <tr><th>参数名字</th><th>类型</th><th>是否必须</th><th>默认值</th><th>其他</th><th>说明</th></tr>
                    {volist name="doc.param" id="param"}
                    <tr>
                        <td>{$param.name|default="-"}</td>
                        <td>{$param.type|default="-"}</td>
                        <td>{if condition="$param.require eq 1"}必填{else/}非必填{/if}</td>
                        <td>{$param.default|default="-"}</td>
                        <td>{$param.other|default="-"}</td>
                        <td>{$param.desc|default="-"}</td>
                    </tr>
                    {/volist}
                </table>
                <br>
                {/if}
                {if condition="isset($doc.remark)"}
                <h3>备注说明</h3>
                <div role="alert" class="alert alert-info">
                    {$doc.remark|default="无"}
                </div>
                <br>
                {/if}
                <h3>返回结果</h3>
                <pre id="json_text">{$return|raw}</pre>
            </div>
            <!--info-->
        </div>


        <br/>
        <div role="alert" class="alert alert-info">
            <strong>提示：此文档是由系统自动生成，如发现错误或疑问请告知开发人员及时修改</strong>
        </div>
    </div>

    <p>&copy; {$copyright} <p>
</div>
{/block}