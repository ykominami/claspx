# require 'pathname'

module Claspx
  class Util
    @logger = nil
    @log_dir_pn = Pathname.new("logs")

    class << self
      def get_new_filename(base_name=nil)
        base_name = "file" if base_name.nil?
        time = Time.new
        %!#{base_name}_#{time.strftime("%Y%m%d%H%M%S")}!
      end

      def get_logfile(name=nil)
        name = get_new_filename() if name.nil?
        @log_dir_pn.mkpath if @log_dir_pn.nil?
        log_pn = @log_dir_pn.join(name)
        File.open(log_pn, "w")
      end

      def get_object_from_json_file(path)
        content = File.read(path)
        JSON.parse(content)
      end

      def get_project_hash(path)
        projects = get_object_from_json_file(path)

        projects.each_with_object({}) do |it, hash|
          hash[it["name"]] = { idx: it["idx"], url: it["url"], github_url_ssh: it["github_url_ssh"], github_url_https: it["github_url_https"] }
        end
      end
    end
  end
end
