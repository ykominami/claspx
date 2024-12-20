# frozen_string_literal: true
require 'pp'

RSpec.describe Claspx::CmdxCmd do
  before(:all){
    @cmdxcmd = Claspx::CmdxCmd.new
    @target_project_name = "__test_project"
  }

  it "リポジトリ削除" do
    project_name = @target_project_name
    result = @cmdxcmd.delete_public_repository(project_name)
    expect(result.success?).to eq(true)
  end

  it "リポジトリ作成" do
    project_name = @target_project_name
    pn = Pathname.new("test_data/github.com/#{project_name}")
    pn.mkpath
    chdir = pn.to_s
    result = @cmdxcmd.create_public_repository(chdir, project_name)
    expect(result.success?).to eq(true)
  end

end
