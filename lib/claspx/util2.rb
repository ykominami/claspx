require 'pathname'

module Claspx
  class Util2
    @output_fname = "b.txt"

    class << self
      def cmdx()
        o, e, s = Open3.capture3("echo 'a'; sort >&2", :stdin_data=>"foo\nbar\nbaz\n")
      end

      def cmd(cmdline, dir=nil)
        if dir.nil?
          o, e, s = Open3.capture3(cmdline)
        else
          o, e, s = Open3.capture3(cmdline, chdir: dir)
        end
        Result.new(s.success?, o)
      end

      def get_max(array)
        array.max
      end

      def repo_list()
        content = File.readlines(@output_fname)
        arr = content.each_with_object([]){ |it, list| 
          array = it.split("\t")
          Loggerx.debug "array.size=#{array.size}"
          ret = array[0].strip.size
          Loggerxcm.debug "ret=#{ret}"
          if array.size > 0
            list << array
          end
        }
      end
    end
  end
end
