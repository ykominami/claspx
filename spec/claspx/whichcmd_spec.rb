
RSpec.describe Claspx::WhichCmd do
  before(:all){
    Claspx::Loggerxcm.init("whichC_", :default, Claspx::LOG_DIR_PN, true, level: :info)

    config = Claspx::Util.get_object_from_json_file("test_data/config.json")
    @top_dir = config["top_dir"]
    # @local_project_name = config["local_project_name"]
    @local_project_name = "2024-Planning"
    # @local_project_name = "/home/ykominamim/repo_ykominami/GAS3/2024-Planning"
    @project_hash = Claspx::Util.get_project_hash("test_data/remote.json")
    @app = Claspx::App.new(@top_dir)
    @app.setup_project_group( @project_hash)
  }

  it "which実行" , clasp_pull: true do
    project = @app.project_group.get_local_project(@local_project_name)
    cmd = Claspx::WhichCmd.new
    result = cmd.which("npx")
    expect(result.status).to eq(true)
  end
end
