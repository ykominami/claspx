# frozen_string_literal: true

require "pp"

RSpec.describe Claspx::GhCmd do
  before(:all) do
    config = Claspx::Util.get_object_from_json_file("test_data/config.json")
    @top_dir = config["top_dir"]
    @local_project_name = config["local_project_name"]
    @project_hash = Claspx::Util.get_project_hash("test_data/remote.json")
    @app = Claspx::App.new(@top_dir)
    @app.setup_project_group(@project_hash)
    @target_dir_pn = Pathname.new("test_data/github.com")
    @target_project_name = "__test_project"
    @non_exist_target_project_name = "__test_project_"
    @ghcmd = described_class.new
  end

  it "github.com上の自分のアカウントのリポジトリ名一覧", list_repo_name: true do
    result = @ghcmd.list_repository_name

    expect(result.status).to eq(true)

    expect(result.return_array.size.positive?).to eq(true)
  end

  it "github.com上の自分のアカウントのリポジトリ一覧", list_repo: true do
    result = @ghcmd.list_repository

    expect(result.status).to eq(true)

    expect(result.return_array.size.positive?).to eq(true)
  end

  it "github.com上の自分のアカウントのリポジトリから指定リポジトリが存在しない", not_found_repo: true do
    result = @ghcmd.find_remote_repository(@non_exist_target_project_name)

    expect(result.status).to eq(false)

    expect(result.return_array.size.zero?).to eq(true)
  end

  it "github.com上の自分のアカウントのパブリックなリポジトリ作成", create_repo: true do
    # ghcmd = Claspx::GhCmd.new
    project_name = @target_project_name
    project_dir_pn = @target_dir_pn.join(project_name)

    gitcmd = Claspx::GitCmd.new(project_dir_pn)
    File.write(project_dir_pn.join("README.md"), "# #{project_name}")
    gitcmd.add("README.md")
    gitcmd.commit("first commit")

    result = @ghcmd.get_or_create_public_repository(project_dir_pn, project_name)
    # p result.message
    expect(result.success?).to eq(true)
  end

  it "github.com上の自分のアカウントのリポジトリから指定リポジトリが存在する", find_repo: true do
    result = @ghcmd.find_remote_repository(@target_project_name)
    # p result.message

    expect(result.success?).to eq(true)

    expect(result.return_array.size.positive?).to eq(true)
  end

  it "github.com上の自分のアカウントのリポジトリから指定リポジトリが削除する", delete_repo: true do
    result = @ghcmd.delete_public_repository(@target_project_name)
    # p result.message

    expect(result.success?).to eq(true)

    expect(result.success?).to eq(true)
  end
end
