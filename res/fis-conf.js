//zseajs 更新seajs config文件
//使用simple插件，自动应用pack的资源引用
fis.config.set('modules.postpackager', 'zseajs,simple');

//开始autoCombine可以将零散资源进行自动打包
//fis.config.set('settings.postpackager.simple.autoCombine', true);
//开启autoReflow使得在关闭autoCombine的情况下，依然会优化脚本与样式资源引用位置
fis.config.set('settings.postpackager.simple.autoReflow', true);

//配置一些zseajs.config的配置项
fis.config.set('zseajs', {
  file: '/common/init.js',
  unAlias: [
    /^\/?libs\//
  ]
});

fis.config.merge({
  settings: {
    optimizer: {
      'uglify-js': {
        mangle: {
          except: ['require']
        },
        compress: {
          hoist_funs: false
        }
      }
    }
  },
  pack: {
    'pkg/libs.js': [
      'libs/seajs/2.3.0/sea.js',
      'libs/seajs/2.3.0/seajs-css.js',
      'libs/jquery/1.11.3/jquery.min.js'
    ],
    'pkg/style.css': [
      'libs/normalize/3.0.3/normalize.min.css',
      'common/style.css'
    ],
    'pkg/modules.css': [
      'modules/button/css/style.css',
      'modules/icon/css/style.css',
      'modules/form/css/style.css'
    ]
  },
  roadmap: {
    path: [{
      //psd源文件不发布
      reg: '**.psd',
      release: false
    }, {
      //libs目录不压缩，不hash
      reg: 'libs/**',
      useCompile: false,
      useHash: false
    }, {
      //占位图片不hash
      reg: 'common/placeholder.png',
      useHash: false
    }, {
      //app、modules目录下的网页图片不hash
      reg: /^\/(app|modules)\/.*\/image\/.*(png|gif|jpg|jpeg)$/i,
      useHash: false
    }]
  }
});