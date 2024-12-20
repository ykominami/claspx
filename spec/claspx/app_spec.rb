# frozen_string_literal: true

RSpec.describe Claspx::App do
  before(:all){
    Claspx::Loggerxcm.init("appC_", :default, Claspx::LOG_DIR_PN, true, level: :info)

    config = Claspx::Util.get_object_from_json_file("test_data/config.json")
    @top_dir = config["top_dir"]
    @local_project_name = config["local_project_name"]
    @project_hash = Claspx::Util.get_project_hash("test_data/remote.json")
    @app = Claspx::App.new(@top_dir)
    @app.setup_project_group( @project_hash)
  }

  it "1つのプロジェクトのセットアップ" , one_project_setup: true do
    project = @app.project_group.get_local_project(@local_project_name)
    result = project.setup

    expect(result.status).to eq(true)
  end

  it "全てのプロジェクトのセットアップ" , all_project_setup: true do
    # pending 'この先はなぜかテストが失敗するのであとで直す'
    result = @app.setup_all

    expect(result.success?).to eq(true)
  end

  it "全てのプロジェクトのclasp clone" , all_project_clasp_clone: true do
    # pending 'この先はなぜかテストが失敗するのであとで直す'
    ret = @app.clasp_clone_all
  end

  it "全てのプロジェクトのclasp pull" , all_project_clasp_pull: true do
    # pending 'この先はなぜかテストが失敗するのであとで直す'
    ret = @app.clasp_pull_all
  end

  it "全てのプロジェクトのJSスクリプトの修正", all_project_trnsform: true do
    @app.setup_all
    ret = @app.transform_js_script_all
    expect(ret.status).to eq(true)
  end

  it "全てのプロジェクトのJS関連ファイルの一覧", all_project_file_list: true do
    @app.setup_all
    ret = @app.transform_js_script_all
    list = @app.file_list
    # list.map{ |it| it.map{ |x|  x.map{ |y|  p y.to_s } } }
    flatten_list = list.flatten(5)
    # flatten_list.map{|x| p x.to_s}
    p flatten_list.size
    expect(flatten_list.size.positive?).to eq(true)
  end

  it "全てのプロジェクトの変換済みJSファイルの一覧", all_project_transformed_file_list: true do
    @app.setup_all
    ret = @app.transform_js_script_all
    flatten_list = @app.transformed_js_list_all().flatten(3)
    # flatten_list.map{|x| p x}
    expect(flatten_list.size.positive?).to eq(true)
  end

  it "全てのプロジェクトの変換済みJSファイルにimportファイル、exportファイルに生成", all_prj_copy_tfile_to_sfile: true do
    @app.setup_all
    ret = @app.transform_js_script_all
    result = @app.make_import_export_file_all()
    
    # return_value = result.return_value
    # list = result.return_array
    # flatten_list.map{|x| p x}
    expect(result.success?).to eq(true)
  end

  # copy_transformed_js_to_src_file_all()
end
