require "claspx"
require 'pp'

comment = ""
comment = "#" if ARGV[0] == "c"

# Claspx::Loggerxcm.init("appC_", :default, Claspx::LOG_DIR_PN, true, level: :debug)

# Claspx::Loggerxcm.init("appC_", :default, Claspx::LOG_DIR_PN, true, level: :info)

# config = Claspx::Util.get_object_from_json_file("test_data/config.json")
# @top_dir = config["top_dir"]
# @local_project_name = "2024-Planning"
@project_hash = Claspx::Util.get_project_hash("test_data/remote.json")
# @app = Claspx::App.new(@top_dir, @project_hash)

path="test_data/clasp/bin"

# pp @project_hash

cmdline = @project_hash.map do |key, project|
  [
  "#{comment} echo '' ",
  "#{comment} echo #{ key }",  
  "#{comment} echo '' ",
  "#{comment} #{path}/clasp_clone ~/repo_ykominami/GAS3/#{key} #{@project_hash[key][:idx]}",
  "#{comment} #{path}/clasp_pull ~/repo_ykominami/GAS3/#{key} #{@project_hash[key][:idx]}"
  ].join("\n")
end

# pp cmdline
File.write("bin/claspx", cmdline.join("\n"))
