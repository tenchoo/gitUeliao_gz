# coding=gbk
import json,os,sys,struct,hashlib

def show_help():
    global filePath
    if len(sys.argv) < 2:
        print """使用格式:\nresmap xxxx.json"""
        sys.exit()
    filePath = sys.argv[1]

def loadFileData(filepath):
    if os.path.isfile( filepath ):
        data = ""
        fp = open( filepath, "r")
        try:
            while True:
                chunk = fp.read(512)
                if not chunk:
                    break
                data = data + chunk
        finally:
            fp.close()
        return data

    else:
        print "文件\"{0}\"不存在".format(filepath)
        sys.exit()

def output( data ):
    res = data["res"]
    pkg = data["pkg"]
    fp = open(filePath+'.php','w')
    fp.write("<?php\nreturn array(\n")
    for key in res:
        md5 = hashlib.md5()
        md5.update(key.strip())
        item = res[key]
        if item.has_key("pkg"):
            val = pkg[ item["pkg"] ]["uri"]
        else:
            val = res[key]["uri"]
        fp.write("\"{0}\" => \"{1}\",\n".format( md5.hexdigest(),val ))
    fp.write(");")
    fp.close()
    print "编译完成"

if __name__ == "__main__":
    show_help()
    data     = loadFileData( filePath )
    jsonData = json.loads( data.encode("utf-8") )
    output( jsonData )


