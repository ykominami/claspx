# frozen_string_literal: true
require 'pp'

RSpec.describe Claspx::GhCmd do
  before(:all){
    config = Claspx::Util.get_object_from_json_file("test_data/config.json")
    @top_dir = config["top_dir"]
    @local_project_name = config["local_project_name"]
    @project_hash = Claspx::Util.get_project_hash("test_data/remote.json")
    @app = Claspx::App.new(@top_dir)
    @app.setup_project_group( @project_hash)
    @target_dir = Pathname.new("test_data/github.com")
    @target_project_name = "__test_project"
    @ghcmd = described_class.new
  }

  it "github.com上の自分のアカウントのリポジトリ名一覧" , list_repo_name: true do
    result = @ghcmd.list_repository_name()
 
    expect(result.status).to eq(true)

    expect(result.return_array.size.positive?).to eq(true)
  end

  it "github.com上の自分のアカウントのリポジトリ一覧" , list_repo: true do
    result = @ghcmd.list_repository()
 
    expect(result.status).to eq(true)

    expect(result.return_array.size.positive?).to eq(true)
  end

  it "github.com上の自分のアカウントのリポジトリから指定リポジトリが存在しない" , not_found_repo: true do
    result = @ghcmd.find_remote_repository(@target_project_name)
 
    expect(result.status).to eq(false)

    expect(result.return_array.size.zero?).to eq(true)
  end

  it "github.com上の自分のアカウントのパブリックなリポジトリ作成" , create_repo: true do
    # ghcmd = Claspx::GhCmd.new
    chdir = @target_dir
    project_name = @target_project_name

    result = @ghcmd.find_remote_repository(project_name)
    return result if result.success?

    # pending 'この先はなぜかテストが失敗するのであとで直す'
    result = @ghcmd.create_public_repository(chdir, project_name)
    p result
    expect(result.status).to eq(true)
  end


  it "github.com上の自分のアカウントのリポジトリから指定リポジトリが存在する" , find_repo: true do
    result = @ghcmd.find_remote_repository(@target_project_name)

    expect(result.status).to eq(true)

    expect(result.return_array.size.positive?).to eq(true)
  end

  it "github.com上の自分のアカウントのリポジトリから指定リポジトリが削除する", delete_repo: true do
    result = @ghcmd.delete_public_repository(@target_project_name)
 
    expect(result.status).to eq(true)

    expect(result.return_array.size.positive?).to eq(true)
  end
end
