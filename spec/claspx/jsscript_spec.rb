RSpec.describe Claspx::Jsscript do
  before(:all) do
    Claspx::Loggerxcm.init("jsscriptC_", :default, Claspx::LOG_DIR_PN, true, level: :info)

    config = Claspx::Util.get_object_from_json_file("test_data/config.json")
    @top_dir = config["top_dir"]
    @local_project_name = "2024-Planning"
    @project_hash = Claspx::Util.get_project_hash("test_data/remote.json")
    @app = Claspx::App.new(@top_dir)
    @app.setup_project_group(@project_hash)
  end

  it "1つのプロジェクトのセットアップ", one_project_setup: true do
    project = @app.project_group.get_local_project(@local_project_name)
    children = project.get_dist_child_list
    src_file = children.first

    # jsscript = Claspx::Jsscript.new(src_file)
    dest_file, basename, parent_dir_of_dest_file = project.get_parnt_dir_and_basename(src_file)
    jsscript = project.register_jsscript(src_file, basename)

    result = jsscript.convertx(dest_file, parent_dir_of_dest_file, basename)
  end
end
