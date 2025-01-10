RSpec.describe Claspx::Geminiaix do
  before(:all){
  }

  it "1つのプロジェクトのセットアップ" , one_project_setup: true do
    Geminiaix.new()
    project = @app.project_group.get_local_project(@local_project_name)
    result = project.setup

    expect(result.status).to eq(true)
  end
end
